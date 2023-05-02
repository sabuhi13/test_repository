<?php declare(strict_types=1);

namespace Saboohy\Conductor;

use function preg_match_all;
use function preg_match;

final class Utils
{
    /**
     * @param string $uri
     * 
     * @return bool
     */
    public static function uriIsParametrized(string $uri = "") : bool
    {
        return (bool) preg_match_all("/{([a-z_:]+)}/", $uri);
    }

    public static function isPrefixable(string $prefix = "") : bool
    {
        return (bool) preg_match("/(?:[a-z]{1,})/i", $prefix);
    }

    public static function isCamelCased(string $prefix = "") : bool
    {
        return (bool) preg_match("/^(?:[a-z]+)$/", $prefix);
    }
}