<?php declare(strict_types=1);

namespace Saboohy\Conductor;

abstract class Interpreter
{
    /**
     * @param string $object_name
     * 
     * @return string
     */
    protected function generateUriFromObjectName(string $object_name = "", string $object_type = "class") : string
    {
        $pattern = $object_type == "class" ? "/([A-Z]{1}[a-z]+)/" : "/([a-zA-Z]{1}[a-z]+)/";

        preg_match_all($pattern, $object_name, $matched);

        $matched = end($matched);
        array_pop($matched);

        $matched = array_map(fn($part) => strtolower($part), $matched);

        return implode("_", $matched);
    }
}