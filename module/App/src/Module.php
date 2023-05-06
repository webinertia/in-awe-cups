<?php

declare(strict_types=1);

namespace App;

use Laminas\I18n\ConfigProvider;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Locale;

use function date_default_timezone_set;

final class Module
{
    /** @var ServiceLocatorInterface $sm */
    protected $sm;
    /** @var array<string, mixed> $config */
    protected $config;

    /** @return array<string, mixed> */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function init(ModuleManager $modulemanager): void
    {
        $events = $modulemanager->getEventManager();
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);
    }

    public function onMergeConfig(ModuleEvent $event): void
    {
        $configListener = $event->getConfigListener();
        $config         = $configListener->getMergedConfig(false);
        if (
            isset($config['session_config']['cookie_secure']) &&
            isset($GLOBALS['_SERVER']['REQUEST_SCHEME']) &&
            ! $config['session_config']['cookie_secure'] &&
            $GLOBALS['_SERVER']['REQUEST_SCHEME'] === 'https'
        ) {
            $config['session_config']['cookie_secure']   = true;
            $config['session_config']['cookie_samesite'] = 'None';
        }
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $app    = $e->getApplication();
        $sm     = $app->getServiceManager();
        $config = $sm->get('config');
        date_default_timezone_set($config['app_settings']['server']['time_zone']);

        $translator = $sm->get(Translator::class);
        if ($config['app_settings']['server']['enable_translation']) {
            // get an instance of the Request object
            $request = $sm->get('Request');
            // what locale has the client set in their browser?
            $locale = Locale::acceptFromHttp($request->getServer()->get('HTTP_ACCEPT_LANGUAGE', 'en_US'));
            // the translator to enabled
            // set the primary locale as requested by the client
            if ($locale !== null) {
                $translator->setLocale($locale);
                // set the fallback
                $translator->setFallbackLocale('en_US');
            }
            $renderer = $sm->get(PhpRenderer::class);
            /**
             * This allows view helpers to automatically translate things like
             * the Menu component if the labels have been translated.
             * Also allows for translating Form validator messages.
             **/
            $renderer->getHelperPluginManager()->configure((new ConfigProvider())->getViewHelperConfig());
        }
    }
}
