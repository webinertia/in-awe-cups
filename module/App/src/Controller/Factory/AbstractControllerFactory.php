<?php

declare(strict_types=1);

namespace App\Controller\Factory;

use App\Form\FormManagerAwareInterface;
use App\Service\AppSettingsAwareInterface;
use App\Session\Container as SessionContainer;
use App\Session\SessionContainerAwareInterface;
use Laminas\Form\FormElementManager;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Stdlib\DispatchableInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Psr\Container\ContainerInterface;
use User\Acl\AclAwareInterface;
use User\Service\UserService;
use User\Service\UserServiceAwareInterface;
use User\Service\UserServiceInterface;

class AbstractControllerFactory extends ReflectionBasedAbstractFactory
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DispatchableInterface
    {
        $controller  = parent::__invoke($container, $requestedName, $options);
        $appSettings = $container->get('config')['app_settings'];
        if ($controller instanceof SessionContainerAwareInterface) {
            $controller->setSessionContainer($container->get(SessionContainer::class));
        }
        if ($controller instanceof AclAwareInterface) {
            $controller->setAcl($container->get(AclInterface::class));
        }
        if ($controller instanceof AppSettingsAwareInterface) {
            $controller->setAppSettings($appSettings);
        }
        if ($controller instanceof FormManagerAwareInterface) {
            $controller->setFormManager($container->get(FormElementManager::class));
        }
        if ($controller instanceof TranslatorAwareInterface) {
            $controller->setTranslator($container->get(Translator::class));
        }
        if ($controller instanceof UserServiceAwareInterface) {
            if ($container->has(UserServiceInterface::class)) {
                $controller->setUserService($container->get(UserServiceInterface::class));
            } else {
                $controller->setUserService($container->get(UserService::class));
            }
        }
        return $controller;
    }
}
