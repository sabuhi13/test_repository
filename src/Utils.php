<?php declare(strict_types=1);

namespace Saboohy\Conductor;

use function preg_match_all;

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

    /**
     * 
     */
    public static function makeRouteName(string $general_prefix = "", string $controller_name = "", string $action_name = "") : string
    {
        
    }
}