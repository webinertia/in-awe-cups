<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\ControllerInterface;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Header\ContentSecurityPolicy;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\MvcEvent;

class ContentSecurityListener extends AbstractListenerAggregate
{
    /** @var ControllerManager $controllerManager */
    protected $controllerManager;
    /** @var array $config */
    protected $config;

    public function __construct(ControllerManager $controllerManager, array $config)
    {
        $this->controllerManager = $controllerManager;
        $this->config            = $config['app_settings']['server']['content_security_policy']['directives'];
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setPolicy']);
    }

    public function setPolicy(MvcEvent $event): void
    {
        // Get an instance of the matched controller
        /** @var \Laminas\Mvc\Controller\AbstractController $controller*/
        $controller = $this->controllerManager->get($event->getRouteMatch()->getParam('controller'));
        if (! $controller instanceof ControllerInterface) {
            return;
        }
        /** @var \Laminas\Http\Response $response */
        $response = $controller->getResponse();
        $headers  = $response->getHeaders();
        $csp      = new ContentSecurityPolicy();
        foreach($this->config as $directive => $value) {
            $csp->setDirective($directive, $value);
        }
        //$headers->addHeader($csp);
    }
}
