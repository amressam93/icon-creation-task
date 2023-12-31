<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    use ResponseTrait;
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request){
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation->errors())->withInput();
            }
            $email = $request->input('email');
            $password = $request->input('password');
            $user = $this->userRepository->getByEmail($email);
            if($user) {
                $result = $this->userRepository->processLogin($user,$password);
                return $this->handleLoginResult($result);
            }
            return redirect()->back()->withErrors(['email' => 'User Not Found'])->withInput();

        } catch (\Exception $e) {
            $message = "Oops Something Went Wrong. Please Try Again Later";
            return redirect()->back()->withErrors(['error' => $message])->withInput();
        }
    }

    private function handleLoginResult($result)
    {
        $status = $result['status'];
        switch ($status) {
            case 'success':
                return redirect()->route('dashboard')->with('success', 'Login Successful');
            case 'blocked':
                return redirect()->back()->withErrors(['error' => 'Your account is blocked. Please contact support for assistance'])->withInput();
            case 'max_devices':
                return redirect()->back()->withErrors(['error' => 'You are logged in from two devices'])->withInput();
            case 'invalid_password':
                return redirect()->back()->withErrors(['error' => 'Invalid password'])->withInput();
            case 'max_attempts':
                return redirect()->back()->withErrors(['error' => 'Please try again after 30 seconds'])->withInput();
        }
    }

    public function dashboard()
    {
        if (auth()->check()) {
            return view('dashboard');
        } else {
            return view('auth.login');
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        // Delete user token associated with the current device
        $user->userTokens()
            ->where('user_agent', $request->userAgent())
            ->delete();

        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

}
