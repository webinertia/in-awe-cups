<?php

declare(strict_types=1);

namespace App\Log;

use App\Log\LogEvent;
use App\Log\LoggerAwareInterface;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\I18n\Translator;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Psr\Log\LoggerInterface;

final class LogListener implements ListenerAggregateInterface, TranslatorAwareInterface
{
    use ListenerAggregateTrait;
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /** @var LoggerInterface $logger */
    protected $logger;
    /** @var Translator $translator */
    protected $translator;

    public function __construct(LoggerInterface $logger, Translator $translator, array $config)
    {
        $this->setLogger($logger);
        $this->translator = $translator;
        //
        if ($config['server']['log_errors']) {
            $log = $this->logger->getLogger();
            $log::registerErrorHandler($log, true);
        }
    }

    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $sharedMananger    = $events->getSharedManager();
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::EMERGENCY,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ALERT,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::CRITICAL,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ERROR,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::WARNING,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::NOTICE,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::INFO,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::DEBUG,
            [$this, 'log'],
            $priority
        );
    }

    public function log(EventInterface $event)
    {
        $passContext = false;
        $name        = $event->getName();
        $logMessage  = $event->getTarget();
        $params      = $event->getParams();
        if ($params !== []) {
            $passContext = true;
        }
        if ($passContext) {
            unset($params['translate']);
            $this->logger->$name($this->getTranslator()->translate($logMessage), $params);
        } else {
            unset($params);
            $this->logger->$name($this->getTranslator()->translate($logMessage));
        }
    }
}
