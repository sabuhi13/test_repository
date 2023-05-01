<?php declare(strict_types=1);

namespace Saboohy\Conductor;

use Saboohy\Conductor\Collector;

abstract class Collection extends Collector
{
    /**
     * @param string $controller
     * 
     * @return void
     */
    public function use(string $controller = "", bool $make_prefix = true) : void
    {
        $this->build($controller, $make_prefix);
    }
}