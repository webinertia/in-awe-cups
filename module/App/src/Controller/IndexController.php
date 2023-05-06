<?php

/**
 * @codingStandardsIgnoreStart
 * @method \App\Controller\Plugin\Email email()
 * @codingStandardsIgnoreEnd
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Form\ContactForm;
use App\Form\FormManagerAwareInterface;
use App\Form\FormManagerAwareTrait;
use App\Log\LogEvent;
use ContentManager\Model\Page;
use RuntimeException;

use function sprintf;

final class IndexController extends AbstractAppController implements FormManagerAwareInterface
{
    use FormManagerAwareTrait;

    /** @var ContactForm $form */
    protected $form;
    /** @var Page $page */
    protected $page;

    public function __construct(Page $page, array $config)
    {
        parent::__construct($config);
        $this->page = $page;
    }

    public function indexAction(): mixed
    {
        if (! isset($this->appSettings['load_store_as_homepage']) || ! $this->appSettings['load_store_as_homepage']) {
            $homePage   = $this->page->getLandingPage();
            $this->view->setVariables([
                'page' => $homePage,
            ]);
        } else {
            $this->view->setVariable(
                'store',
                $this->forward()->dispatch(
                    'CategoriesController',
                    ['action' => 'index', 'name' => 'all', 'showHeader' => false, 'setActive' => false]
                )
            );
        }
        if ($this->config['module_settings']['widget']['imageslider']['enable_imageslider']) {
            $this->layout()->setVariables([
                'isHomePage' => true,
                'slider'     => $this->forward()->dispatch(
                    'ImageSliderController',
                    ['action' => 'index', 'slideCount' => '2']
                ),
            ]);
        }
        return $this->view;
    }

    public function contactAction(): mixed
    {
        $form        = $this->formManager->get(ContactForm::class);
        $appSettings = $this->getService('config')['app_settings'];
        if ($this->request->isPost()) {
            $validationGroup = ['fullName', 'email', 'message'];
            if ($appSettings['security']['enable_captcha']) {
                $validationGroup[] = 'captcha';
            }
            $form->setValidationGroup($validationGroup);
            $form->setData($this->request->getPost()->toArray());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $this->email()->contactUsMessage($data->email, $data->fullName, $data->message);
                } catch (RuntimeException $e) {
                    $this->getEventManager()->trigger(
                        LogEvent::NOTICE,
                        sprintf(
                            $this->getTranslator()->translate('log_contact_email_failure'),
                            $data->email,
                            $data->fullName
                        )
                    );
                    $this->flashMessenger()->addErrorMessage(
                        $this->getTranslator()->translate('contact_email_failure')
                    );
                }
                $this->getEventManager()->trigger(
                    LogEvent::INFO,
                    sprintf(
                        $this->getTranslator()->translate('log_contact_email_success'),
                        $data->email,
                        $data->fullName
                    )
                );
                $this->getEventManager()->trigger(
                    LogEvent::INFO,
                    sprintf(
                        $this->getTranslator()->translate('log_contact_email_success'),
                        $data->fullName,
                        $data->email
                    )
                );
                $this->flashMessenger()->addSuccessMessage(
                    $this->getTranslator()->translate('contact_email_success')
                );
                return $this->redirect()->toRoute('home');
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
