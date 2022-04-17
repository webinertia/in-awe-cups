<?php

declare(strict_types=1);

namespace Application\Form\Factory;

use Application\Form\ContactForm;
use Application\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webinertia\ModelManager\ModelManager;

class ContactFormFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ContactForm
    {
        return new ContactForm($container->get(ModelManager::class)->get(Settings::class));
    }
}
