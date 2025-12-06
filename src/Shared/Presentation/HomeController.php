<?php

namespace Shared\Presentation;

use Latte\Engine;

class HomeController
{
    private Engine $latte;

    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

    public function index(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Home.latte',
            []
        );
    }
}
