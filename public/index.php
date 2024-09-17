<?php

use App\Models\Post;
use App\Models\User;
use Dotenv\Dotenv;
use Routes\Route;

session_start();

date_default_timezone_set("Asia/Dhaka");

CONST BASE_PATH = __DIR__.'/../';

require BASE_PATH.'vendor/autoload.php';

require BASE_PATH.'app/helpers/helpers.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

require base_path('routes/web.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$startPos = false;
$path = __DIR__;
if (preg_match('/\/(assets|images)/', $uri, $matches)) 
{
    $startPos = strpos($uri, $matches[0]);
}
if ($startPos) 
{
    $subPath = substr($uri, $startPos);
    $path = $path.$subPath;
}


$fileExtension = pathinfo($path, PATHINFO_EXTENSION);
if ($fileExtension && file_exists($path)) 
{
    switch (strtolower($fileExtension)) 
    {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        default:
            header('Content-Type: application/octet-stream');
            break;
    }
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
// dd(route("home", 1));
Route::router($uri, $method);












