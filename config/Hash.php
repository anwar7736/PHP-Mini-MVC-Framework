<?php
namespace Config;


class Hash {
    public static function make($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }   
    
    public static function check($new, $old)
    {
        return password_verify($new, $old);
    }
}