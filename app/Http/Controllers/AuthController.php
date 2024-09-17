<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Auth;
use App\Models\Post;
use App\Validation\Validator;
use Config\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public $errors = [];

    public function __construct()
    {
        
    }
    
    public function loginView()
    {
        return view('auth.login');
    }    
    
    public function registerView()
    {
        return view('auth.register');
    }        
    
    public function login()
    {
        extract($_POST);

        if(!Validator::string($email))
        {
            $this->errors['email'] = 'This field is required!';
        }          
        
        else if(!Validator::email($email))
        {
            $this->errors['email'] = 'Please enter valid email address!';
        }        
        
        if(!Validator::string($password))
        {
            $this->errors['password'] = 'This field is required!';
        }        

        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }

        if(Auth::attempt($email, $password))
        {
            return response([
                'success' => true, 
                'message'  => "Login Successfully.",
                'url'      => route("home"),
            ]);
        }

        $this->errors['email'] = 'Email address or Password is incorrect!';
        

        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }
        


    }    
    
    public function register()
    {
        extract($_POST);

        if(!Validator::string($name))
        {
            $this->errors['name'] = 'This field is required!';
        }         
        
        if(!Validator::string($email))
        {
            $this->errors['email'] = 'This field is required!';
        }          
        
        else if(!Validator::email($email))
        {
            $this->errors['email'] = 'Please enter valid email address!';
        }                
        
        if(!Validator::string($password))
        {
            $this->errors['password'] = 'This field is required!';
        }        
        
        else if(!Validator::string($password, 4))
        {
            $this->errors['password'] = 'Password must be atleast 4 characters!';
        }

        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }
        
        $user = User::where("email", $email)->first();

        if($user)
        {
            $this->errors['email'] = 'Email address already exists!';
        }

        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }

        $user = User::create([
						'name' => $name,
						'email' => $email,
						'password' => Hash::make($password),
					]);

        if($user)
        {
            Auth::login($email, $password);
            return response([
                'success' => true, 
                'message'  => "Register Successfully.",
                'url'      => route("home"),
            ]);
        }
        
    }    
    
    public function logout()
    {
       Auth::logout();
       return redirect(route('home'));
    }

    public function myProfile()
    {
        // User::where("id", causer_id())->first();
        // dd(Post::where("id", 4)->toSql());
        return view('auth.profile');
    }    
    
    public function updateProfile()
    {
        extract($_POST);

        if(!Validator::string($name))
        {
            $this->errors['name'] = 'This field is required!';
        }         
        
        if(!empty($old_password))         
        {
            if(!Hash::check($old_password, Auth::password()))
            {
                $this->errors['old_password'] = 'Old password does not match!';
            }

            if(!Validator::string($new_password))
            {
                $this->errors['new_password'] = 'This field is required!';
            }            
            
            else if(!Validator::string($new_password, 4))
            {
                $this->errors['new_password'] = 'Password must be atleast 4 characters!';
            }
        }
        if(!empty($this->errors))
        {
            return response([
                'success' => false, 
                'errors'  => $this->errors
            ]);
        }

        $image_name = Auth::image();
        $password = Auth::password();

        if(!empty($_FILES['image']['name']))
        {
            deleteFile('users', $image_name);
            $image_name = uploadFile('users', 'image');
        }

        if(!empty($new_password))
        {
            $password =  Hash::make($new_password);
        }

        $updated = User::where("id", causer_id())
                   ->update([
						'name' => $name,
						'password' => $password,
						'image' => $image_name,
					]);

        if($updated)
        {
            $user = User::findOrFail(causer_id());
            session('user', $user);
            return response([
                'success' => true, 
                'message'  => "Your profile has been updated successfully!",
                'url'      => route('my-profile'),
            ]);
        }
        
    }


}