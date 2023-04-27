<?php declare(strict_types=1);

namespace Saboohy\Conductor\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Middleware
{
    private array $middlewares = [];

    public function __construct(...$middlewares)
    {
        $this->middlewares = $middlewares;
    }
}