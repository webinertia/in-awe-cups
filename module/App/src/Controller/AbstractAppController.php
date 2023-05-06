<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\ControllerInterface;
use App\Controller\Trait\AjaxActionTrait;
use App\Controller\Trait\QueryParamsTrait;
use App\Service\AppSettingsAwareTrait;
use App\Session\SessionContainerAwareTrait;
use Laminas\Form\FormElementManager;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Model\ViewModel;
use User\Acl\AclAwareTrait;
use User\Acl\CheckActionAccessTrait;
use User\Acl\ResourceAwareTrait;
use User\Service\UserServiceAwareTrait;

/**
 * Abstract App Controller
 * Plugin and trait method signatures for static analysis
 * @codingStandardsIgnoreStart
 * @method \App\Controller\Plugin\Email email()
 * @method \App\Controller\Plugin\ServiceLocator getService(string $serviceName)
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 * @method \User\Controller\Plugin\Acl acl()
 * @method \User\Acl\CheckActionAccessTrait isAllowed(?ResourceInterface $resourceInterface = null, ?string $privilege = null)
 * @method \User\Controller\Plugin\Identity identity()
 * @method \Laminas\Http\PhpEnvironment\Request getRequest()
 * @method \Laminas\Http\PhpEnvironment\Response getResponse()
 * @codingStandardsIgnoreEnd
 */

abstract class AbstractAppController extends AbstractActionController implements ControllerInterface
{
    use AclAwareTrait, CheckActionAccessTrait {
        CheckActionAccessTrait::isAllowed insteadof AclAwareTrait;
    }
    use AjaxActionTrait;
    use AppSettingsAwareTrait;
    use CheckActionAccessTrait;
    use ResourceAwareTrait;
    use SessionContainerAwareTrait;
    use TranslatorAwareTrait;
    use UserServiceAwareTrait;
    use QueryParamsTrait;

    /** @var Request $request */
    protected $request;
    /** @var Response $response */
    protected $response;
    /** @var string $appPath */
    public $appPath;
    /** @var string $baseUrl */
    public $baseUrl;
    /** @var string $basePath */
    public $basePath;
    /** @var string $referringUrl */
    public $referringUrl;
    /** @var int|string $resourceId */
    protected $resourceId;
    /** @var ViewModel $view */
    protected $view;
    /** @var array<string, mixed> $config */
    protected $config;
    /** @var FormElementManager $formElementManager */
    protected $formElementManager;

    /**
     * @return void
     * @param array<string, mixed> $config
     * */
    public function __construct(array $config) {
        $this->config   = $config;
        $this->view     = new ViewModel();
        $this->appPath  = $this->config['app_settings']['server']['app_path'];
        $this->basePath = $this->appPath;

        $this->view->setVariables([
            'resourceId' => null,
        ]);
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->ajaxAction();
        parent::onDispatch($e);
    }
}
