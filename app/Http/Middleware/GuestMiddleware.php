<?php
namespace App\Http\Middleware;
use App\Http\Middleware\Middleware;
class GuestMiddleware extends Middleware
{
    public function handle()
    {
        if(isset($_SESSION['user']))
        {
            return redirect('./');
        }
    }
}