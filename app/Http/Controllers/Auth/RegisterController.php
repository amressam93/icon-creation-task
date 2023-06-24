<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ResponseTrait;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ResponseTrait;
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function __invoke(Request $request){
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validation->fails()) {
                return $this->validationResponse($validation);
            }
            $validatedData = $validation->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = $this->userRepository->create($validatedData);
            $token = Auth::guard('api')->login($user, true);
            $data = [
                'user' => new UserResource(Auth::guard('api')->user()),
                'token' => 'Bearer ' . $token,
            ];
            $message = "Registered Successfully";
            return $this->successResponse($message,$data);
        }
        catch (\Exception $e) {
            $message = "Oops Something Went Wrong Please Try Again Later";
            return $this->logicErrorResponse($message);
        }
    }
}
