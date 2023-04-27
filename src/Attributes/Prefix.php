<?php declare(strict_types=1);

namespace Saboohy\Conductor\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
class Prefix {
    public function __construct(
        private string $prefix = ""
    ) {}
}