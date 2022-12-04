<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Controller\IndexController;
use App\Log\LogEvent;
use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareInterfaceTrait;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use User\Service\UserService;

use function sprintf;

class AdminListener extends AbstractListenerAggregate implements LoggerAwareInterface
{
    use LoggerAwareInterfaceTrait;

    /** @var array<mixed> $config */
    protected $config;
    /** @var AbstractAppController $controller */
    protected $controller;
    /** @var ControllerManager $controllerManager */
    protected $controllerManager;
    /** @var TemplateMapResolver */
    protected $templateMapResolver;
    /** @var Translator $translator */
    protected $translator;
    /** @return void */
    public function __construct(
        ControllerManager $controllerManager,
        TemplateMapResolver $templateMapResolver,
        Translator $translator,
        array $config
    ) {
        $this->config = $config;
        $this->controllerManager   = $controllerManager;
        $this->templateMapResolver = $templateMapResolver;
        $this->translator          = $translator;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'authorize']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setAdminLayout']);
    }

    public function authorize(MvcEvent $event): void
    {
        // Get an instance of the matched controller
        $this->controller = $this->controllerManager->get($event->getRouteMatch()->getParam('controller'));
        // Check if the request is trying to access an admin controller, if not return early
        if (! $this->controller instanceof AdminControllerInterface) {
            return;
        }
        /** @var UserService $user */
        $user = $this->controller->identity()->getIdentity();
        if (
            ! $this->controller->identity()->hasIdentity() ||
            ! $this->controller->acl()->isAllowed($user, $this->controller, 'view')
        ) {
            $action       = $this->translator->translate('admin_access_action');
            $logMessage   = $this->translator->translate('log_forbidden_known_action_403');
            $flashMessage = $this->translator->translate('forbidden_known_action_403');
            // we better log this
            $this->getEventManager()->trigger(
                LogEvent::WARNING,
                sprintf(
                    $logMessage,
                    $action
                )
            );
            // let them know were paying attention
            $this->controller->flashMessenger()->addErrorMessage(
                sprintf(
                    $flashMessage,
                    $action
                )
            );
            // send them here instead and let them know their action has been logged
            $event->getRouteMatch()->setParam('controller', IndexController::class);
            $event->getRouteMatch()->setParam('action', 'index');
        }
    }

    public function setAdminLayout(MvcEvent $event): void
    {
         // Get and check the route match object
        $routeMatch = $event->getRouteMatch();
        if (! $routeMatch) {
             return;
        }
         // Get and check the parameter for current controller
        $this->controller = $routeMatch->getParam('controller');
        $this->controller = $this->controllerManager->get($this->controller);
        $name             = $this->config['admin_template'];
        // if this is not an admin controller or if we have already got the layout return
        if (! $this->controller instanceof AdminControllerInterface || $this->templateMapResolver->has($name)) {
             return;
        }
        // Get root view model
        $layoutViewModel = $event->getViewModel();
        // Rendering without layout? This will be the case of all ajax requests
        if ($layoutViewModel->terminate()) {
            return;
        }
        // Change template
        $layoutViewModel->setTemplate($name);
    }
}
