<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\SettingsForm;
use App\Form\ThemeSettingsForm;
use App\Log\LogEvent;
use App\Model\Theme;
use Laminas\Config\Exception\RuntimeException as ConfigRuntimeException;
use Laminas\Config\Factory as ConfigFactory;
use Laminas\Config\Writer\PhpArray as ConfigWriter;
use Laminas\Json\Json;
use Laminas\Form\FormElementManager;
use Laminas\Http\Header\SetCookie;;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\ModelInterface;
use RuntimeException;
use User\Controller\WidgetController;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'admin';
    /** @var Theme $theme */
    protected $theme;

    public function __construct(FormElementManager $formElementManager, Theme $theme, array $config)
    {
        parent::__construct($config);
        $this->formElementManager = $formElementManager;
        $this->theme = $theme;
    }

    public function indexAction(): ModelInterface
    {
        $this->view->setVariable('cmd', $this->request->getQuery('cmd', 'bypass'));
        return $this->view;
    }

    public function manageThemesAction(): ViewModel
    {
        $this->ajaxAction();
        $this->view->setVariable('message', 0);
        $headers = $this->response->getHeaders();
        if (! $this->isAllowed($this->theme)) {
            $this->view->setVariables(['error' => true, 'message' => 'Access denied']);
        }
        /** @var ThemeSettingsForm $form */
        $form = $this->formElementManager->get(ThemeSettingsForm::class);
        $form->setAttribute('action', $this->url()->fromRoute('admin/themes/manage'));
        if (! $this->request->isPost()) {
            $form->setData($this->theme->getConfig());
        }
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $writer = new ConfigWriter();
                    $writer->setUseBracketArraySyntax(true);
                   // $writer->toFile($this->theme->getConfigPath(), $data);
                    $this->view->setVariable('message', 'Theme Settings saved!!');
                } catch (RuntimeException $e) {
                    $this->getEventManager()->trigger(LogEvent::ERROR, $e->getMessage());
                }
            } else {
                $errors = $form->getMessages();
                //$this->response->setStatusCode(406);
                //$this->response->setReasonPhrase("Invalid Form Data");
                // return new JsonModel(
                //     [
                //         'messageType' => 'formError',
                //         'formErrors'  => $form->getMessages(),
                //         'message'     => 'Invalid data received.',
                //     ]
                // );
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function manageSettingsAction(): ModelInterface
    {
        $this->resourceId = 'settings';
        $headers          = $this->response->getHeaders();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->isAllowed($this)) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $form = $this->formElementManager->get(SettingsForm::class);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin/settings/manage')
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $writer = new ConfigWriter();
                $writer->setUseBracketArraySyntax(true);
                try {
                    $config = $form->getData();
                    $writer->toFile(
                        $this->basePath . '/config/autoload/appsettings.local.php',
                        $form->getData(),
                    );
                    return new JsonModel(['message' => 'Settings saved!!']);
                } catch (ConfigRuntimeException $e) {
                    $this->getEventManager()->trigger(
                        LogEvent::CRITICAL,
                        $this->getTranslator()->translate('log_settings_file_write_failure')
                        . 'Exception Info: '
                        . $e->getFile() . ' Line#:' . $e->getLine() . ': ' . $e->getMessage()
                    );
                    return new JsonModel(['errors' => $form->getMessages()]);
                }
                $headers->addHeaderLine('Content-Type', 'application/json');
                $this->getEventManager()->trigger(
                    LogEvent::NOTICE,
                    $this->getTranslator()->translate('log_settings_file_write_success')
                );
                $this->view->setVariables(
                    [
                        'success' => true,
                        'message' => $this->getTranslator()->translate('edit_settings_success'),
                        'form' => $form
                    ]
                );
                return $this->view;
            } else {
                return $this->view;
            }
        } else {
            try {
                $config = ConfigFactory::fromFile($this->basePath . '/config/autoload/appsettings.local.php');
                if (isset($config['app_settings'])) {
                    $form->setData($config);
                } else {
                    throw new RuntimeException('log_settings_file_read_failure');
                }
            } catch (ConfigRuntimeException $e) {
                $this->getEventManager()->trigger(
                    LogEvent::CRITICAL,
                    $this->getTranslator()->translate($e->getMessage())
                    . 'Exception Info: '
                    . $e->getFile() . ' Line#:' . $e->getLine()
                );
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function addsettingAction(): mixed
    {
        $this->resourceId = 'settings';

        if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
            $this->flashMessenger()->addWarningMessage(
                'Access Denied, your attempt to access this area as been logged'
            );
            $this->response->setStatusCode(403);
        }
        $this->view->setVariable('resourceId', $this->resourceId);
        return $this->view;
    }
}
