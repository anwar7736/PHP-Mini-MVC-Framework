<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Auth;
use App\Http\Request;
use App\Models\Post;
use Config\Validator;
use Config\Hash;
use App\Models\User;
use Config\Log;

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
    
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);      

            if($validator->fails())
            {
                return response([
                    'success' => false, 
                    'errors'  => $validator->errors()
                ]);
            }

            if(Auth::attempt($validator->validated()))
            {
                return response([
                    'success' => true, 
                    'message' => "Login Successfully.",
                    'url'     => route("home"),
                ]);
            }
            
            return response([
                'success' => false, 
                'message' => 'Email address or password is incorrect!'
            ]);

        } catch (\Throwable $th) {
            writeException($th);
            return response([
                'success' => false, 
                'message'  => $th->getMessage()
            ]);
        }
    }    
    
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'min:3'],
                'phone' => ['required', 'unique:users,phone'],
                'email' => ['nullable', 'email', 'unique:users,email'],
                'password' => ['required', 'min:4', 'confirmed'],
                'avatar' => ['nullable', 'image']
            ]);      

            if($validator->fails())
            {
                return response([
                    'success' => false, 
                    'errors'  => $validator->errors()
                ]);
            }

            $inputs = $validator->validated();
            $inputs['password'] = Hash::make($inputs['password']);
            if($request->hasFile('avatar'))
            {
                $inputs['avatar'] = uploadFile('users', $request->file('avatar'));
            }
            $user = User::create($inputs);
            if($user)
            {
                Auth::login($user);
                return response([
                    'success' => true, 
                    'message' => "Register Successfully.",
                    'url'     => route("home"),
                ]);
            }
        } catch (\Throwable $th) {
            writeException($th);
            return response([
                'success' => false, 
                'message'  => $th->getMessage()
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
        return view('auth.profile');
    }    
    
    public function updateProfile(Request $request)
    {
        try {
            $rules = [
                'name'   => ['required', 'min:3'],
                'avatar' => ['nullable', 'image']
            ];
            
            $old_password = $request->input('old_password');    
            
            if(!empty($old_password))         
            {
                if(!Hash::check($old_password, Auth::password()))
                {
                    $this->errors['old_password'] = 'Old password does not match!';
                }

                $rules['password'] = ['required', 'min:4', 'confirmed'];
            }

            $validator = Validator::make($request->all(), $rules);      

            if($validator->fails() || !empty($this->errors))
            {
                return response([
                    'success' => false, 
                    'errors'  => array_merge($this->errors, $validator->errors())
                ]);
                
            }

            $image_name = Auth::image();
            $password = Auth::password();
            $inputs   = $validator->only('name');
            if($request->hasFile('avatar'))
            {
                deleteFile('users', $image_name);
                $inputs['avatar'] = uploadFile('users', $request->file('avatar'));
            }

            if(!empty($request->input('password')))
            {
                $inputs['password'] =  Hash::make($request->input('password'));
            }
            // dd($inputs);
            $updated = User::where("id", causer_id())->update($inputs);

            if($updated)
            {
                $user = User::findOrFail(causer_id());
                session('user', $user);
                return response([
                    'success'  => true, 
                    'message'  => "Your profile has been updated successfully!",
                    'data'     => $user,
                    'url'      => route('my-profile'),
                ]);
            }
        } catch (\Throwable $th) {
            writeException($th);
            return response([
                'success' => false, 
                'message'  => $th->getMessage(),
            ]);
        }
    }
}