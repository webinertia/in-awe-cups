<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\ControllerInterface;
use App\Controller\Trait\AjaxActionTrait;
use App\Service\AppSettingsAwareTrait;
use App\Session\SessionContainerAwareTrait;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\View\Model\JsonModel;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Mvc\Controller\AbstractRestfulController;
use User\Acl\AclAwareTrait;
use User\Acl\CheckActionAccessTrait;
use User\Acl\ResourceAwareTrait;
use User\Service\UserServiceAwareTrait;

class AbstractApiController extends AbstractRestfulController implements ControllerInterface
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

    /**
     * @return void
     * @param array<string, mixed> $config
     * */
    public function __construct(array $config) {
        $this->config   = $config;
        $this->view     = new JsonModel();
        $this->appPath  = $this->config['app_settings']['server']['app_path'];
        $this->basePath = $this->appPath;

        $this->view->setVariables([
            'resourceId' => null,
        ]);
    }
}
