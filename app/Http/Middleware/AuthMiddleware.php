<?php
namespace App\Http\Middleware;
use App\Http\Middleware\Middleware;
class AuthMiddleware extends Middleware
{
    public function handle()
    {
        if(!isset(auth()->user()))
        {
            return redirect('./login');
        }
    }
}
