<?php
namespace App\Http\Controllers\Auth;  
use Config\Hash;
use App\Models\User;
class Auth 
{
    public function __construct()
    {
        
    }

    public static function attempt($inputs)
    {
        $user = User::where("email", $inputs['email'])->first();

        if($user && Hash::check($inputs['password'], $user->password))
        {
            session('user', $user);
            return true;
        }
        
        return false;

    }    
    
    public static function login($user)
    {
        session('user', $user);
    }    
    
    public static function logout()
    {
        session_destroy();
    }    
    
    public static function user()
    {
        return session('user') ?? '';
    }    
    
    public static function id()
    {
        return session('user')->id ?? '';
    }    
    
    public static function name()
    {
        return session('user')->name ?? '';
    }    
    
    public static function email()
    {
        return session('user')->email ?? '';
    }    
    
    public static function password()
    {
        return session('user')->password ?? '';
    }    
    
    public static function image()
    {
        return session('user')->avatar ?? '';
    }
    
}