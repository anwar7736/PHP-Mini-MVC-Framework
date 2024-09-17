<?php
namespace Config;


class App {
    protected static $bindings = [];
    public static function bind($key, $callback)
    {
        static::$bindings[$key] = $callback;
    }

    public static function make($key)
    {
        if(array_key_exists($key, static::$bindings))
        {
            $callback = static::$bindings[$key];
            return call_user_func($callback);
        }

        else throw new \Exception('No matching binding found for '.$key);
    }
}