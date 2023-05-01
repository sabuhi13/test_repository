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

        /*-------------------------------------------------------------*
         | Making auto prefix from controller name                     |
         *-------------------------------------------------------------*/
        if ( $make_prefix ) {

            # Get parts of controller name
            preg_match_all(
                "/([A-Z]{1}[a-z]+)/", 
                $class->getShortName(), 
                $matched_parts
            );

            # Last object
            $matched_parts = end($matched_parts);

            # Remove "Controller"
            array_pop($matched_parts);

            # Convert to lower
            $matched_parts = array_map(
                fn($part) => strtolower($part),
                $matched_parts
            );

            $prefixes["controller_prefix"] = implode("_", $matched_parts);
        }
        
        /*-------------------------------------------------------------*
         | Building route path                                         |
         *-------------------------------------------------------------*/
        if ( !empty($prefixes) ) {
            $route_path = implode("/", array_values($prefixes));
        }

        /*-------------------------------------------------------------*
         | Seting general middleware                                   |
         *-------------------------------------------------------------*/
        if ( property_exists($this, "middleware") ) {

            if ( !class_exists($this->middleware) ) {
                throw new \Exception("middleware not found!");
            }

            $general_middleware = [$this->middleware, "handle"];
            
            $middlewares[] = $general_middleware;
        }

        # 
        /*-------------------------------------------------------------*
         | Checking and collecting class attributes                    |
         | ------------------------------------------------------------|
         | * Expecting only Middleware                                 |
         *-------------------------------------------------------------*/
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

        /*-------------------------------------------------------------*
         | Checking and collecting method attributes                   |
         |-------------------------------------------------------------|
         | * Expecting Route and Middleware                            |
         *-------------------------------------------------------------*/
        foreach ( $class->getMethods() as $class_method ) {

            $class_method_name          = $class_method->getName();
            $class_method_attributes    = $class_method->getAttributes();

            $used_attributes_on_method  = [];

            foreach ($class_method_attributes as $class_method_attribute) {
                $used_attributes_on_method[$class_method_name][] = $class_method_attribute->getName();
            }

            foreach ( $used_attributes_on_method as $key => $details ) {
                if ( count(array_diff($details, $this->expectMethodAttributes)) > 0 ) {
                    throw new \Exception("Unexpected attribute on $class_method_name method");
                }
            }
        }

        var_dump($route_path);     
    }

    /**
     * @param string $controller
     * 
     * @return void
     */
    protected function registerController(string $controller = "") : void
    {
        $class = new ReflectionClass($controller);

        // $general_prefix = property_exists($this, "prefix") ? $this->prefix : "";
        $prefix = "";

        // $class_prefixes = $class->getAttributes(Prefix::class);

        // if ( count($class_prefixes) > 0 ) {
        //     $prefix = $general_prefix.$class_prefixes[0]->getArguments()[0];
        // }

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