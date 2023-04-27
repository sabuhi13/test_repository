<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test;

use PHPUnit\Framework\TestCase;
use Saboohy\Conductor\Test\Collections\{
    AdminCollection
};
use Saboohy\Conductor\Test\Controllers\{
    BasicController,
    FullController
};
use Saboohy\Conductor\Test\Middlewares\{
    GeneralMiddleware,
    GeneralControllerMiddleware,
    IndexMiddleware,
    StoreMiddleware,
    GetMiddleware,
    UpdateMiddleware,
    DeleteMiddleware
};

final class CollectionTest extends TestCase
{
    // public function setUp()
    // {

    // }

    public function testAdminCollection()
    {
        $adminCollection = new AdminCollection();

        $basicControllerClass = [
            "GET" => [
                "/admin/basic/" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [BasicController::class, "index"]
                    ],
                    "name" => "admin_basic_index"
                ],
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [BasicController::class, "get"]
                    ],
                    "name" => "admin_basic_get"
                ]
            ],
            "POST" => [
                "/admin/basic/" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [BasicController::class, "store"]
                    ],
                    "name" => "admin_basic_store"
                ]
            ],
            "PUT" => [
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [BasicController::class, "update"]
                    ],
                    "name" => "admin_basic_update"
                ]
            ],
            "DELETE" => [
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [BasicController::class, "delete"]
                    ],
                    "name" => "admin_basic_delete"
                ]
            ]
        ];

        $fullControllerClass = [
            "GET" => [
                "/admin/basic/" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [GeneralControllerMiddleware::class, "handle"],
                        [IndexMiddleware::class, "handle"],
                        [FullController::class, "index"]
                    ],
                    "name" => "admin_full_index"
                ],
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [GeneralControllerMiddleware::class, "handle"],
                        [GetMiddleware::class, "handle"],
                        [FullController::class, "get"]
                    ],
                    "name" => "admin_full_get"
                ]
            ],
            "POST" => [
                "/admin/basic/" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [GeneralControllerMiddleware::class, "handle"],
                        [StoreMiddleware::class, "handle"],
                        [FullController::class, "store"]
                    ],
                    "name" => "admin_full_store"
                ]
            ],
            "PUT" => [
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [GeneralControllerMiddleware::class, "handle"],
                        [UpdateMiddleware::class, "handle"],
                        [FullController::class, "update"]
                    ],
                    "name" => "admin_full_update"
                ]
            ],
            "DELETE" => [
                "/admin/basic/{id}" => [
                    "actions" => [
                        [GeneralMiddleware::class, "handle"],
                        [GeneralControllerMiddleware::class, "handle"],
                        [DeleteMiddleware::class, "handle"],
                        [FullController::class, "delete"]
                    ],
                    "name" => "admin_full_delete"
                ]
            ]
        ];

        $array_must_be = array_merge_recursive($basicControllerClass, $fullControllerClass);

        var_dump($adminCollection->controllers());
    }

    // public function testInitialCollection()
    // {
        
    // }
}