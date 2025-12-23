<?php
namespace App\Http\Middleware;
use App\Http\Middleware\Middleware;
class GuestMiddleware extends Middleware
{
    public function handle()
    {
        if(isset(auth()->user()))
        {
            return redirect('./');
        }
    }
}
