<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Collections;

use Saboohy\Conductor\Collection;
use Saboohy\Conductor\Test\Middlewares\GeneralMiddleware;
use Saboohy\Conductor\Test\Controllers\{
    BasicController,
    FullController
};

final class AdminCollection extends Collection
{
    /**
     * General prefix
     * 
     * @var string
     */
    protected string $prefix = "/admin";

    /**
     * General middleware
     * 
     * @var string
     */
    protected string $middleware = GeneralMiddleware::class;

    /**
     * Collections
     * 
     * @return void
     */
    protected function collect() : void
    {
        // $this->use(BasicController::class);
        $this->use(FullController::class);
    }
}