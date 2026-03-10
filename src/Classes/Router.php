<?php

namespace Src\Classes;

use Src\Exceptions\RouteNotFoundException;

class Router{
    private static $routes = [];
    private static $middleware = [];

    public static function register($method, $url , $action)
    {
        if(! isset(static::$routes[$method][$url])){

            static::$routes[$method][$url] = [
                "action" => $action, 
                "middleware" => static::$middleware
                ];
        }
    }

    public static function get($url, $action)
    {
        static::register('GET', $url, $action);
    }

    public static function post($url, $action)
    {
        static::register('POST', $url, $action);
    }

    public static function group($options, $callback)
    {
        if (isset($options['middleware'])) {
            static::$middleware = $options['middleware'];
        }

        $callback();

        static::$middleware = [];
    }

    public static function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && $basePath !== '\\') {
            $url = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $url, 1);
        }
        $url = '/' . trim($url, '/');
        if ($url === '//') {
            $url = '/';
        }


        if(isset(static::$routes[$method][$url])){
            $route = static::$routes[$method][$url];
            $action = $route['action'];
            $middleware = $route['middleware'];

            foreach ($middleware as $m) {
                [$m, $param] = explode(":", $m);
                
                if(! empty($params)){
                    (new $m())->handle($param);
                }else{
                    (new $m())->handle();
                }
            }

            [$class, $method] = $action;
            if(class_exists($class) && method_exists($class, $method)){
                $class = new $class();
                return call_user_func_array([$class, $method], []);
            }

        }else{
            throw new RouteNotFoundException();
        }
    }
}
