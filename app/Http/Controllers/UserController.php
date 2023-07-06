<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Traits\ResponseTrait;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseTrait;
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->getAllOrderedByIndexes();
        $data['users'] = new UserCollection($users);
        return $this->successResponse("User data retrieved successfully",$data);
    }

    public function list(){
        $users = $this->userRepository->getAllOrderedByIndexes()->paginate(10);
        return view('users.index', ['users' => $users]);
    }
}
