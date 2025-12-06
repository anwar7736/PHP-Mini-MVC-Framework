<?php
namespace Config;
class Schema
{
    public static function create($table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $query = $blueprint->execute('create');
    }

    public static function table($table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $query = $blueprint->execute('alter');
    }

    public static function dropIfExists($table)
    {
        $blueprint = new Blueprint($table);
        $query = $blueprint->execute('drop');
    }
}
