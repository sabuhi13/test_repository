<?php declare(strict_types=1);

namespace Saboohy\Conductor;

use Saboohy\Conductor\Interpreter;
use Saboohy\Conductor\Attributes\{
    Middleware,
    Prefix,
    Route
};
use ReflectionClass;
use ReflectionMethod;

use function properity_exists;
use function strtoupper;
use function array_map;
use function array_merge_recursive;

abstract class Collector extends Interpreter
{   
    /**
     * Controllers
     * 
     * @var array
     */
    private array $controllers = [];

    /**
     * 
     */
    private array $map = [];

    /**
     * Routes
     * 
     * @var array
     */
    private array $routes = [];

    private array $components = [];

    public function __construct()
    {
        $this->collect();
    }

    protected function build(string $controller = "", bool $make_prefix = true)
    {
        $class = new ReflectionClass($controller);
    }

    protected function buildComponent(string $controller = "") : void
    {
        $class = new ReflectionClass($controller);

        $route_path = "";

        $general_prefix = "";

        if ( property_exists($this, "prefix") ) {
            $general_prefix = !empty($this->prefix) ? $this->prefix : "";
        }

        # Get parts of controller name
        preg_match_all(
            "/([A-Z]{1}[a-z]+)/", 
            $class->getShortName(), 
            $matched_parts
        );

        $matched_parts = end($matched_parts);

        # Remove "Controller"
        array_pop($matched_parts);

        $matched_words = array_map(
            fn($word) => strtolower($word), 
            $matched_parts
        );

        $class_prefix = implode("_", $matched_words);

        # Concat prefixes
        // $route_path .= "/" . $class_prefix;

        var_dump($class_prefix);
    }

    /**
     * @param string $controller
     * 
     * @return void
     */
    protected function registerController(string $controller = "") : void
    {
        $class = new ReflectionClass($controller);

        $general_prefix = property_exists($this, "prefix") ? $this->prefix : "";
        $prefix = "";

        $class_prefixes = $class->getAttributes(Prefix::class);

        if ( count($class_prefixes) > 0 ) {
            $prefix = $general_prefix.$class_prefixes[0]->getArguments()[0];
        }

        $general_middleware = property_exists($this, "middleware") ? $this->middleware : "";

        $general_middlewares = [];

        if ( !empty($general_middleware) ) {
            $general_middlewares[] = [$general_middleware, "handle"];
        }

        $class_middlewares = $class->getAttributes(Middleware::class);
        $general_controller_middlewares = array_map(fn($row) => [$row, "handle"], $class_middlewares[0]->getArguments());

        if ( count($class_middlewares) > 0 ) {
            $general_middlewares = array_merge_recursive($general_middlewares, $general_controller_middlewares);
        }

        foreach ( $class->getMethods() as $class_method ) {

            [$action_method, $action_path] = $class_method->getAttributes(Route::class)[0]->getArguments();

            $action_middlewares = $class_method->getAttributes(Middleware::class)[0]->getArguments();

            $this->map[$class->getShortName()][strtoupper($action_method)][$prefix.$action_path] = [
                "action"    => $this->makeAction(
                    $general_middlewares,
                    array_map(fn($row) => [$row, "handle"], $action_middlewares),
                    [$class->getName(), $class_method->getName()]
                ),
                "name"      => ""
            ];
        }

        print_r($this->map);
    }

    private function makeAction($general_middleware, $action_middleware, $action_handler)
    {
        $action = array_merge_recursive($general_middleware, $action_middleware);

        $action[] = $action_handler;

        return $action;
    }

    public function controllers() : array
    {
        return $this->controllers;
    }

    /**
     * Collected routes
     * 
     * @return array
     */
    public function routes() : array
    {
        return $this->routes;
    }
}