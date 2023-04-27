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

    public function __construct()
    {
        $this->collect();
        // $this->build();
    }

    /**
     * @param string $controller
     * 
     * @return void
     */
    protected function registerController(string $controller = "") : void
    {
        $class = new ReflectionClass($controller);
        $class_short_name = $class->getShortName();

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

        if ( count($class_middlewares) > 0 ) {
            $general_middlewares[] = array_map(fn($row) => [$row, "handle"], $class_middlewares[0]->getArguments());
        }

        foreach ( $class->getMethods() as $class_method ) {

            [$action_method, $action_path] = $class_method->getAttributes(Route::class)[0]->getArguments();

            $action_path = $prefix.$action_path;

            $action_middlewares = $class_method->getAttributes(Middleware::class)[0]->getArguments();

            $this->map[$class_short_name][strtoupper($action_method)][$action_path] = [
                "general_middlewares"   => $general_middlewares,
                "action_middlewares"    => array_map(fn($row) => [$row, "handle"], $action_middlewares),
                "action"                => [$class->getName(), $class_method->getName()],
                "name"                  => ""
            ];
        }

        print_r($this->map);
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