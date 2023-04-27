<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test\Controllers;

use Saboohy\Conductor\Attributes\{
    Prefix,
    Route
};

#[Prefix("/basic")]
class BasicController
{
    #[Route("get", "/")]
    public function index()
    {
        # code ...
    }

    #[Route("post", "/")]
    public function store()
    {
        # code ...
    }

    #[Route("get", "/{id}")]
    public function get($id)
    {
        # code ...
    }

    #[Route("put", "/{id}")]
    public function update($id)
    {
        # code ...
    }

    #[Route("delete", "/{id}")]
    public function delete($id)
    {

    }
}