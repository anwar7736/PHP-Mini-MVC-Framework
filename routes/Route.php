<?php
namespace Routes;
use App\Http\Middleware\Middleware;

class Route {

    public static $routes = [];

    public static function add($method, $uri, $controller)
    {
        static::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'name' => '',
            'middleware' => '',
        ];

        return new self;
    }        
    
    public static function get($uri, $controller)
    {
        return static::add('GET', $uri, $controller);
    }    
    
    public static function post($uri, $controller)
    {
        return static::add('POST', $uri, $controller);
    }    
    
    public static function put($uri, $controller)
    {
        return static::add('PUT', $uri, $controller);
    }    
    
    public static function patch($uri, $controller)
    {
        return static::add('PATCH', $uri, $controller);
    }    
    
    public static function delete($uri, $controller)
    {
        return static::add('DELETE', $uri, $controller);
    }
    
    public static function middleware($name)
    {
        static::$routes[array_key_last(static::$routes)]['middleware'] = $name;
        return new static;
    }

    public static function name($name)
    {
        static::$routes[array_key_last(static::$routes)]['name'] = $name;
        return new static;
    }

    public static function router($uri, $method)
    {
        $status = 0;   
        $customURI = explode("/", $uri);
        $rootDir = "";
        if(isset($customURI[1]))
        {
            $rootDir = "/".$customURI[1];
        }
        foreach(static::$routes as $route)
        {
            $id = isset($customURI[3]) ? $customURI[3] : '';
            $route['uri'] = str_replace("{id}", $id, $route['uri']);
            if($rootDir.$route['uri'] == $uri && $route['method'] == $method)
            {
                Middleware::resolve($route['middleware']);
                $data = $route['controller'];        
                $object = new $data[0];
                $method = $data[1];
                $object->$method($id);
                $status = 1;
            }
            
        }

        if($status == 0)
        {
            abort();
        }
    }
}