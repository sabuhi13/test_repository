<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Middlewares;

final class GeneralControllerMiddleware
{
    public function handle(callable $next)
    {
        return $next();
    }
}