<?php

namespace Shared\Presentation;

use Latte\Engine;

class HomeController
{
    private Engine $latte;

    public function __construct()
    {
        $this->latte = new Engine();
        $this->latte->setTempDirectory(__DIR__ . '/../../../temp/cache');
    }

    public function index(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Home.latte',
            []
        );
    }
}
