<?php declare(strict_types=1);

namespace Saboohy\Conductor\Test;

use PHPUnit\Framework\TestCase;
use Saboohy\Conductor\Utils;

final class UtilsTest extends TestCase
{
    /**
     * @return void
     */
    public function testUriIsParametrized() : void
    {
        $this->assertTrue(
            Utils::uriIsParametrized("/path/{int:post_id}/{cat_id}")
        );
    }
}