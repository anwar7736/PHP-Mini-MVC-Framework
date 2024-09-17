<?php
namespace App\Validation;

class Validator {
    public static function string($string, $min = 1, $max = INF)
    {
        $string_length = strlen(trim($string));

        return $string_length >= $min && $string_length <= $max;
    }           
    
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }    
    
    public static function image($image)
    {
        
    }
}