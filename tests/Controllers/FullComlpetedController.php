<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Controllers;

use Saboohy\Conductor\Attributes\{
    Middleware,
    Prefix,
    Route
};
use Saboohy\Conductor\Test\Middlewares\{
    GeneralControllerMiddleware,
    IndexMiddleware,
    StoreMiddleware,
    GetMiddleware,
    UpdateMiddleware,
    DeleteMiddleware
};

#[
    Prefix("/full"),
    Middleware(GeneralControllerMiddleware::class)
]
class FullComlpetedController
{
    #[
        Route("get", "/"),
        Middleware(IndexMiddleware::class)
    ]
    public function index()
    {
        # code ...
    }

    #[
        Route("post", "/"),
        Middleware(StoreMiddleware::class)
    ]
    public function store()
    {
        # code ...
    }

    #[
        Route("get", "/{id}"),
        Middleware(GetMiddleware::class)
    ]
    public function get($id)
    {
        # code ...
    }

    #[
        Route("put", "/{id}"),
        Middleware(UpdateMiddleware::class)
    ]
    public function update($id)
    {
        # code ...
    }

    #[
        Route("delete", "/{id}"),
        Middleware(DeleteMiddleware::class)
    ]
    public function delete($id)
    {
        # code ...
    }
}