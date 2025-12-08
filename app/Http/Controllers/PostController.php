<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Auth; 
use Config\Validator;
use Config\Response;
use App\Models\Post;
use App\Models\User;
use App\Http\Request;
use Config\DB;
use Config\Log;

class PostController extends Controller 
{
    public $errors = [];
    public function __construct()
    {

    }

    public function index()
    {
        $posts = DB::table('posts')->join("users u", "u.id", "posts.user_id")
        ->select("posts.*", 'u.name')
        ->latest()
        ->get();
    
                 
        return view('posts.index', compact('posts'));
    }       
    
    public function create()
    {
        return view('posts.create');
    }    
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'between:10,100'],
                'body'  => ['required', 'between:1,1500'],
                'image' => ['nullable', 'image'],
            ]);      

            if($validator->fails())
            {
                return response([
                    'success' => false, 
                    'errors'  => $validator->errors()
                ]);
            }

            $inputs = $validator->validated();    

            if($request->hasFile('image'))
            {
                $inputs['image'] = uploadFile('posts', $request->file('image'));
            }

            $inputs['user_id'] = causer_id();

            $post = Post::create($inputs);

            if($post)
            {
                return response([
                    'success' => true, 
                    'message' => "Your post has been published now!",
                    'data'    => $post,
                    'url'     => route('my-post'),
                ]);
            }
        } catch (\Throwable $th) {
            writeException($th);
             return response([
                'success' => false, 
                'message' => $th->getMessage(),
            ]);
        }
    }
    
    public function show($id)
    {
        // dd($id);
        $post = Post::join("users u", "u.id", "posts.user_id")
                ->where("posts.id", $id)
                ->orderByDesc("posts.created_at")
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
        $post = Post::join("users as u", "u.id", "posts.user_id")
                ->where("posts.id", $id)
                ->orderByDesc("posts.created_at")
                ->select("posts.*", "u.name")
                ->first();
		
        if($post && $post->user_id == causer_id())
        {
            return view('posts.edit', compact('post'));
        }

        abort(Response::FORBIDDEN);
       
    }    
    
    public function update(Request $request, $id)
    {
       try {

            $post = Post::find($id);
            if($post && $post->user_id != causer_id())
            {
               abort(Response::FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'between:10,100'],
                'body'  => ['required', 'between:1,1500'],
                'image' => ['nullable', 'image'],
            ]);      

            if($validator->fails())
            {
                return response([
                    'success' => false, 
                    'errors'  => $validator->errors()
                ]);
            }

            $inputs = $validator->validated();

            $image_name = $post->image;

            if($request->hasFile('image'))
            {
                deleteFile('posts', $image_name);
                $image_name = uploadFile('posts', $request->file('image'));
            }

            $inputs['image'] = $image_name;

            $updated = Post::where("id", $id)->update($inputs);
                        
            if($updated)
            {
                return response([
                    'success' => true, 
                    'message' => "Your post has been updated now!",
                    'data'    => $post,
                    'url'     => route('my-post'),
                ]);
            } 

       } catch (\Throwable $th) {
            writeException($th);
            return response([
                'success' => false, 
                'message' => $th->getMessage(),
            ]);
       }
    }
    
    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            if($post && $post->user_id != causer_id())
            {
                abort(Response::FORBIDDEN);
            }
            
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
            
        } catch (\Throwable $th) {
            writeException($th);
            return response([
                'success' => false, 
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function myPost()
    {
        $posts = Post::join("users", "users.id", "posts.user_id")
                ->where("posts.user_id", causer_id())
                ->orderByDesc("posts.created_at")
                ->select("posts.*", "users.name")
                ->get();

        return view('posts.my-post', compact('posts'));
    }  


}