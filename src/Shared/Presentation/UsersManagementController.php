<?php

namespace Shared\Presentation;

use Latte\Engine;

class UsersManagementController
{
    private Engine $latte;

    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

    public function index(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/UsersManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario')
            ]
        );
    }
}
