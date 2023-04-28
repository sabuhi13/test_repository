<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Collections;

class SiteCollection
{
    protected string $prefix = "";

    protected string $middleware = "";

    protected function collect() : void
    {
        $this->use(SiteController::class, false);
    }
}