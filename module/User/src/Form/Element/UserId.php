<?php

declare(strict_types=1);

namespace User\Form\Element;

use Laminas\Form\Element\Hidden;
use User\Service\UserService;

final class UserId extends Hidden
{
    /** @var UserService $userService */
    protected $userService;
    /** @var mixed */
    protected $value;
    /** @var boolean */
    protected $hasValue = false;

    public function __construct(UserService $userService, $name = null, iterable $options = [])
    {
        parent::__construct($name, $options);
        $this->userService = $userService;
        $this->setValue($this->userService->id);
    }
}
