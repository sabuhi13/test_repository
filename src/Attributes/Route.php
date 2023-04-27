<?php declare(strict_types=1);

namespace Saboohy\Conductor\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        private string $method   = "GET",
        private string $path     = "/"
    ) {}
}