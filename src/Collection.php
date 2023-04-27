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
    public function use(string $controller = "") : void
    {
        $this->registerController($controller);
    }
}