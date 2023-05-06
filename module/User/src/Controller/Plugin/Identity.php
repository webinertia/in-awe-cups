<?php

declare(strict_types=1);

namespace User\Controller\Plugin;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Service\UserService;
use User\Service\UserServiceInterface;

final class Identity extends AbstractPlugin
{
    /** @var AuthenticationService $authenticationService */
    protected $authenticationService;
    /** @var UserService $userInterface */
    protected $userInterface;

    public function __construct(AuthenticationService $authenticationService, UserServiceInterface $userInterface)
    {
        $this->authenticationService = $authenticationService;
        $this->userInterface         = $userInterface;
    }

    public function __invoke(): self
    {
        return $this;
    }

    public function identity(): self
    {
        return $this;
    }

    /**
     * @param string $name
     * @param array<int, mixed> $arguments
     */
    public function __call($name, $arguments): mixed
    {
        return $this->authenticationService->$name(...$arguments);
    }

    public function getIdentity(): UserService
    {
        if ($this->authenticationService->hasIdentity()) {
            return $this->userInterface->fetchRow('userName', $this->authenticationService->getIdentity());
        }
        return $this->userInterface;
    }

    public function getLogData(): array
    {
        return $this->userInterface->getLogData();
    }
}
