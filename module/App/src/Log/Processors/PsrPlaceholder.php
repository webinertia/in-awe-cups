<?php

declare(strict_types=1);

namespace App\Log\Processors;

use Laminas\Log\Processor\PsrPlaceholder as Placeholder;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use User\Service\UserService;

use function array_merge;

final class PsrPlaceholder extends Placeholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /** @var UserService $userService */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function process(array $event): array
    {
        if ($event['extra'] === []) {
            $event['extra'] += $this->userService->getLogData();
        } elseif ($event['extra'] !== []) {
            $event['extra'] = array_merge($this->userService->getLogData(), $event['extra']);
        }
        return parent::process($event);
    }
}
