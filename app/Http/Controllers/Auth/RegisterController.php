<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    public function register(Request $request){
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation->errors())->withInput();
            }
            $validatedData = $validation->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = $this->userRepository->create($validatedData);
            if($user){
                return redirect()->route('login')->with('success', 'Registered Successfully');
            }
        }
        catch (\Exception $e) {
            $message = "Oops Something Went Wrong Please Try Again Later";
            return $this->logicErrorResponse($message);
        }
    }
}
