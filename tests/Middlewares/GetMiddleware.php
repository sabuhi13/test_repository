<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Middlewares;

final class GetMiddleware
{
    public function handle(callable $next)
    {
        return $next();
    }
}