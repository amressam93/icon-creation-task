<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ResponseTrait;
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request){
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validation->fails()) {
                return $this->validationResponse($validation);
            }
            $email = $request->input('email');
            $password = $request->input('password');
            $user = $this->userRepository->getByEmail($email);
            if($user) {
                $result = $this->userRepository->processLogin($user, $password);
                return $this->handleLoginResult($result);
            }
            return $this->notFoundResponse("User Not Found");

        } catch (\Exception $e) {
            $message = "Oops Something Went Wrong. Please Try Again Later";
            return $this->logicErrorResponse($message);
        }
    }

    private function handleLoginResult($result)
    {
        $status = $result['status'];
        $data = $result['data'];
        switch ($status) {
            case 'success':
                return $this->successResponse("Login Successful", $data);
            case 'blocked':
                return $this->forbiddenResponse("Your account is blocked");
            case 'max_devices':
                return $this->forbiddenResponse("You're logged in from two devices");
            case 'invalid_password':
                return $this->unauthorizedResponse("Invalid password");
            case 'max_attempts':
                return $this->customResponse("Please try again after 30 seconds", null, 429);
        }
    }

    public function logout(Request $request)
    {
        $user = auth('api')->user();
        // Delete user token associated with the current device
        $user->userTokens()
            ->where('user_agent', $request->userAgent())
            ->delete();
        auth('api')->logout();
        return $this->successResponse("Logged Out");
    }
}
