<?php declare(strict_types=1);

namespace Saboohy\Conductor;

use Saboohy\Conductor\{
    Interpreter,
    Utils
};
use Saboohy\Conductor\Attributes\{
    Middleware,
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
     * @var array
     */
    private array $expectClassAttributes = [
        \Saboohy\Conductor\Attributes\Middleware::class
    ];

    /**
     * @var array
     */
    private array $expectMethodAttributes = [
        \Saboohy\Conductor\Attributes\Route::class,
        \Saboohy\Conductor\Attributes\Middleware::class
    ];

    /**
     * Routes
     * 
     * @var array
     */
    private array $routes = [];

    public function __construct()
    {
        $this->collect();
    }

    /**
     * @param string $controller
     * @param bool $make_prefix
     * 
     * @return void
     */
    protected function build(string $controller = "", bool $make_prefix = true) : void
    {
        $class = new ReflectionClass($controller);

        $route_path = "";
        $prefixes = [];

        $middlewares = [];
        $general_middleware = null;
        
        if ( property_exists($this, "prefix") && Utils::isPrefixable($this->prefix) ) {
            $prefixes["general_prefix"] = $this->prefix;
        }

        # Making auto prefix from controller name
        if ( $make_prefix ) {

            preg_match_all(
                "/([A-Z]{1}[a-z]+)/", 
                $class->getShortName(), 
                $matched_parts
            );

            $matched_parts = end($matched_parts);

            array_pop($matched_parts);

            $matched_parts = array_map(
                fn($part) => strtolower($part),
                $matched_parts
            );

            $prefixes["controller_prefix"] = implode("_", $matched_parts);
        }
        
        # Building route path
        if ( !empty($prefixes) ) {
            $route_path = implode("/", array_values($prefixes));
        }

        # Seting general middleware
        if ( property_exists($this, "middleware") ) {

            if ( !class_exists($this->middleware) ) {
                throw new \Exception("middleware not found!");
            }

            $general_middleware = [$this->middleware, "handle"];
            
            $middlewares[] = $general_middleware;
        }

        # Checking and collecting class attributes
        $class_attributes = $class->getAttributes();

        if ( count($class_attributes) > 0 ) {
            
            $used_class_attributes = [];

            foreach ( $class_attributes as $class_attribute ) {
                $used_class_attributes[] = $class_attribute->getName();
            }

            $non_expected_attributes = array_diff($used_class_attributes, $this->expectClassAttributes);

            if ( count( $non_expected_attributes ) > 0 ) {
                throw new \Exception("Found non expected attribute");
            }

            # Append used class middleware(s)
            foreach ( $class_attributes[0]->getArguments() as $used_class_middleware ) {
                $middlewares[] = [$used_class_middleware, "handle"];
            }
        }

        # Checking and collecting method attributes
        foreach ( $class->getMethods() as $class_method ) {

            $class_method_name          = $class_method->getName();
            $class_method_attributes    = $class_method->getAttributes();

            $used_attributes_on_method  = [];

            foreach ( $class_method_attributes as $class_method_attribute ) {
                $used_attributes_on_method[$class_method_name][] = $class_method_attribute->getName();
            }

            foreach ( $used_attributes_on_method as $key => $details ) {
                if ( count(array_diff($details, $this->expectMethodAttributes)) > 0 ) {
                    throw new \Exception("Unexpected attribute on $class_method_name method");
                }
            }

            # Collecting method middlewares
            $get_method_middlewares = $class_method->getAttributes(Middleware::class);

            if ( count($get_method_middlewares) > 0 ) {
                $method_middlewares = $get_method_middlewares[0]->getArguments();
            }

            # Getting route
            $method_route = $class_method->getAttributes(Route::class);

            if ( empty($method_route) ) {
                throw new \Exception("Not used Route attribute");
            }

            $route_args = $method_route[0]->getArguments();

            $is_parametrized_route = isset($route_args[0]) && isset($route_args[1]);     
            
            if ( !$is_parametrized_route ) {
                throw new \Exception("Route is not parametrized");
            }

            [$action_method, $action_path] = $route_args;

            $this->routes[strtoupper($action_method)][$route_path.$action_path] = $this->buildRouteDetails(
                general_middleware:     $middlewares,
                action_middleware:      isset($method_middlewares) ? array_map(fn($method_middleware) => [$method_middleware, "handle"], $method_middlewares) : [],
                action_handler:         [$class->getName(), $class_method->getName()],
                prefixes:               array_values($prefixes),
                action_method:          $class_method_name
            );
        }     
    }

    /**
     * @param array<array,string> $args
     * 
     * @return array
     */
    private function buildRouteDetails(array|string ...$args) : array
    {
        $action = array_merge_recursive($args["general_middleware"], $args["action_middleware"]);
        $action[] = $args["action_handler"];

        $args["prefixes"][] = $args["action_method"];
        
        $name = implode("_", $args["prefixes"]);
        
        return [
            "action" => $action,
            "name" => $name
        ];
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