<?php

use App\Http\Controllers\Auth\Auth;
use Config\App;
use Config\Database;
use Config\Response;
use App\Models\Post;
use Config\Route;
use Config\Log;

function writeException($th)
{
    Log::error('Exception caught', [
        'message' => $th->getMessage(),
        'file'    => $th->getFile(),
        'line'    => $th->getLine(),
        'code'    => $th->getCode(),
        'trace'   => $th->getTraceAsString(),
        'previous' => $th->getPrevious() ? $th->getPrevious()->getMessage() : null,
    ]);
}

function base_path($path)
{
    return BASE_PATH.$path;
}

function getDBConnection()
{
    App::bind('Config\Database', function(){
        $config   = require base_path('Config/config.php');
        $default  = $config['default'];
        $dbConfig = $config['connections'][$default];
        return new Database($dbConfig, $dbConfig['username'], $dbConfig['password']);
    });
    
    return App::make('Config\Database');
}

function dd(...$args)
{
    echo "<pre>";
    var_dump($args);
    die();
}

function abort($code = Response::NOT_FOUND)
{
    http_response_code($code); 
    return view($code, compact('code'));
    die();
}

function isActive($navURI)
{
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'])['path']);
    $uri = "/".explode("/", $uri)[2];
    return $uri == $navURI ? 'active' : '';
}

function view($url, $data = [])
{
    extract($data);
    $url = str_replace('.', '/', $url);
    require base_path('resources/views/'.$url.'.blade.php');
}

function url($url)
{
    $rootURI = $_SERVER["PHP_SELF"];
    $rootURI = str_replace('public/index.php', '', $rootURI);
    $url = $rootURI.ltrim($url, "/");
    return $url;
}

function route($name, $id = "")
{
    $routes = Route::$routes;
    $url = "";
    foreach ($routes as $key => $route) 
    {
        if($route["name"] == $name)
        {
            $url = url( str_replace("{id}", $id, $route["uri"]) );
            break;
        }
    }

    return $url ?? url($name);
    
}

function redirect($url)
{
    header("location: {$url}");
}

function old($name)
{
    return $_POST[$name] ?? '';
}

function uploadFile($folder, $file)
{
    // Check if a file was uploaded
    if ($file && isset($file['tmp_name']) && $file['tmp_name'] != '') {

        // Get file extension
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        // Generate a random filename
        $name = rand(100000, 999999) . '.' . $extension;

        // Create folder if it doesn't exist
        $uploadDir = 'images/' . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Full path
        $uploadPath = $uploadDir . '/' . $name;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $name;
        }
    }

    return "";
}

function deleteFile($folder = null, $file = null)
{
    if(!empty($folder) && !empty($file))
    {
         $path = './images/'.$folder.'/'.$file;
         if(file_exists($path))
         {
            unlink($path);
         }

         return true;
    }
}

function getFilePath($folder = null, $file = null)
{
    $path = "images/default/no_image.jpg";
    if(!empty($folder) && !empty($file))
    {
        $path = 'images/'.$folder.'/'.$file;
        if(file_exists($path))
        {
             return $path;
        }
    }

    return $path;
}

function asset($path)
{
    $filePath = "";
    if(!empty($path))
    {
        if(file_exists($path))
        {
            $filePath = $path;
        }
    }

    return $filePath;
}

function session($key, $value = '')
{
    if(!empty($value))
    {
        $_SESSION[$key] = $value;
    }    
    
    else
    {
       return $_SESSION[$key] ?? '';
    }
   
}

function destroy($key)
{
    if(isset($_SESSION[$key]))
    {
        unset($_SESSION[$key]);
    }    
   
}

function auth_post_count()
{
    $total_posts = Post::where("user_id", causer_id())->count();
    return $total_posts;
}

function included($url, $data = [])
{
    extract($data);
    $url = str_replace('.', '/', $url);
    include base_path('resources/views/'.$url.'.blade.php');
}

function response($response)
{
    echo json_encode($response);
    return;
}

function auth()
{
    return new Auth();
}

function user()
{
    return auth()->user();
}

function causer_id()
{
    return user()->id ?? null;
}

function env($key, $default = "")
{
    return $_ENV[$key] ?? $default;
}


