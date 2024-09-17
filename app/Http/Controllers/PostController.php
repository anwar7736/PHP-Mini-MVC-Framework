<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Auth; 
use App\Validation\Validator;
use Config\Response;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller 
{
    public $errors = [];
    public function __construct()
    {

    }

    public function index()
    {
        $posts = Post::join("users u", "u.id", "posts.user_id")
                 ->orderByDesc("posts.published_at")
                 ->select("posts.*", "u.name")
                 ->get();
                 
        return view('posts.index', compact('posts'));
    }       
    
    public function create()
    {
        return view('posts.create');
    }    
    
    public function store()
    {
        extract($_POST);
        $image_name = null;
        if(!Validator::string($title))
        {
            $this->errors['title'] = 'This field is required!';
        }         
        
        if(!Validator::string($body, 1, 1500))
        {
            $this->errors['body'] = 'Post description must be between 1 and 1500 characters!';
        }          
        
        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }

        $image_name = uploadFile('posts', 'image');

        $created = Post::create([
							'user_id' => causer_id(),
							'title' => $title,
							'body' => $body,
							'image' => $image_name,
							'published_at' => date('Y-m-d H:i:s'),
						]);

        if($created)
        {
            return response([
                'success' => true, 
                'message'  => "Your post has been published now!",
                'url'     => route('my-post'),
            ]);
        }
    }
    
    public function show($id)
    {
        $post = Post::join("users u", "u.id", "posts.user_id")
                ->where("posts.id", $id)
                ->orderByDesc("posts.published_at")
                ->select("posts.*", "u.name")
                ->first();
        if($post)
        {
            if($post->user_id != causer_id())
            {
                $view_count = Post::where("id", $id)->sum("view");
                Post::where("id", $id)->update(["view" => $view_count + 1]);
            }
        
            return view('posts.show', compact('post'));
        }
    } 
    
    public function edit($id)
    {
        $post = Post::join("users u", "u.id", "posts.user_id")
                ->where("posts.id", $id)
                ->orderByDesc("posts.published_at")
                ->select("posts.*", "u.name")
                ->first();
		
        if($post && $post->user_id == causer_id())
        {
            return view('posts.edit', compact('post'));
        }
       
    }    
    
    public function update($id)
    {
        extract($_POST);
        $post = Post::find($id);

        if($post && $post->user_id == causer_id())
        {
            if(!Validator::string($title))
            {
                $this->errors['title'] = 'This field is required!';
            }         
            
            if(!Validator::string($body, 1, 1500))
            {
                $this->errors['body'] = 'Post description must be between 1 and 1500 characters!';
            }          
            
            if(!empty($this->errors))
            {
                return response([
                    'success' => false, 
                    'errors'  => $this->errors
                ]);
            }

            $image_name = $post->image;

            if(!empty($_FILES['image']['name']))
            {
                deleteFile('posts', $image_name);
                $image_name = uploadFile('posts', 'image');
            }

            $updated = Post::where("id", $id)->update([
							'title' => $title,
							'body' => $body,
							'image' => $image_name,
						]);

                        
            if($updated)
            {
                return response([
                    'success' => true, 
                    'message' => "Your post has been updated now!",
                    'url'     => route('my-post'),
                ]);
            }
       
        }  
    }
    
    public function destroy($id)
    {
        $post = Post::find($id);
        if($post && $post->user_id == causer_id())
        {
            deleteFile('posts', $post->image);
            $deleted = Post::where("id", $id)->delete();
            if($deleted)
            {
                return response([
                    'success' => true, 
                    'message' => "Your post has been deleted now!",
                    'url'     => route('my-post')
                ]);
            }
        }
    }

    public function myPost()
    {
        $posts = Post::join("users", "users.id", "posts.user_id")
                ->where("posts.user_id", causer_id())
                ->orderByDesc("posts.published_at")
                ->select("posts.*", "users.name")
                ->get();

        return view('posts.my-post', compact('posts'));
    }  


}