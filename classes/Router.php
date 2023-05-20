<?php

namespace classes;

class Router
{
    // list of methods that can not be invoked from URL
    private static array $protected_methods = ['show'];

    public static function redirect(string $controller = "Home", string $method = "show") {
        $url = "Location: ".url;
        if($controller != "Home") {
            $url .= "?u=".$controller;
            if ($method != "show")
                $url .= "/" . $method;
        }
        header($url);
        exit();
    }

    /**
     * Parse query string and run appropriate controller/method
     *
     * @param string $controller
     * @param string $method
     */
    public static function run(string $controller = 'Home', string $method = 'show') {
        $_POST = self::cleanPOST($_POST);

        $query = array();
        parse_str($_SERVER['QUERY_STRING'], $query);

        // parse query
        if(isset($query['u'])) {
            $path = explode('/', $query['u']);

            if(isset($path[0])) {
                $controller = $path[0];
            }

            if(isset($path[1])) {
                $method = $path[1];
                if (in_array($method, self::$protected_methods)) {
                    self::show404();
                    return;
                }
            }
        }

        // load controller file
        $controllerFile = __DIR__."/../controllers/".$controller.".php";
        if(!file_exists($controllerFile)) {
            self::show404();
            return;
        }
        require_once ($controllerFile);

        // Use reflection to check if method exists and invoke it
        try {
            $reflector = new \ReflectionClass('\\controllers\\'.$controller);

            if($reflector->hasMethod($method))
            {
                $namespaceController = $reflector->getNamespaceName()."\\".$controller;

                $object = new $namespaceController();
                $methodReflector = $reflector->getMethod($method);
                $methodReflector->invoke($object);
            }
            else
            {
                self::show404();
            }
        } catch (\ReflectionException $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Recursively clean POST (or GET) array with htmlspecialchars
     * @param array $post
     * @return array
     */
    public static function cleanPOST(array $post): array
    {
        $r = array();
        foreach($post as $key => $value) {
            if(is_array($value))
                $r[$key] = self::cleanPOST($value);
            else
                $r[$key] = htmlspecialchars($value);
        }
        return $r;
    }


    /**
     * Page does not exist
     */
    private static function show404() {
        echo "Page does not exist";
    }
}