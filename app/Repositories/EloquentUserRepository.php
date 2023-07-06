<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class EloquentUserRepository implements UserRepository
{
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getById(int $id): ?User
    {
        return User::find($id);
    }

    public function update(User $user): bool
    {
        return $user->save();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getAllOrderedByIndexes(): object
    {
        return User::orderBy('email')->orderBy('is_blocked');
    }

    public function isAccountBlocked(User $user): bool
    {
        return $user->is_blocked;
    }

    public function blockAccount(User $user): bool
    {
        $user->is_blocked = true;
        return $this->update($user);
    }

    public function resetLoginAttempts(User $user): bool
    {
        $user->login_attempts = 0;
        $user->last_login_attempt = now();
        return $this->update($user);
    }

    public function incrementLoginAttempts(User $user): bool
    {
        $user->login_attempts++;
        return $this->update($user);
    }

    public function verifyPassword(string $password, User $user): bool
    {
        return Hash::check($password, $user->password);
    }

    public function generateUserToken(): string
    {
        $token = '0123456789abcdefghijklmnopqrstuvwxyz'.md5(microtime());
         return str_shuffle($token);
    }

    public function createUserToken(User $user): void{
        $token = $this->generateUserToken(); // Generate a random token
        // Check if the generated token already exists in the database
        if (UserToken::where('token', $token)->exists()) {
            $token =  $this->generateUserToken();
        }
        $user->userTokens()->create([
            'token' => $token,
            'user_agent' => request()->header('User-Agent')
        ]);
    }

    public function getUserTokenCount(User $user):int{
        return $user->userTokens()->count();
    }

    public function isMaxDevicesReached(User $user): bool
    {
        $maxDevices = 2;
        $userTokenCount = $this->getUserTokenCount($user);
        return $userTokenCount >= $maxDevices;
    }

    public function processLogin(User $user, string $password,$api = false): array
    {
        if ($this->isMaxDevicesReached($user)) {
           return $this->handleLoginResponse('max_devices');
        }
        if ($this->isAccountBlocked($user)) {
            return $this->handleLoginResponse('blocked');
        }
        if ($this->verifyPassword($password, $user)) {
            $this->resetLoginAttempts($user);
            $this->createUserToken($user);
            if($api){
                return $this->loginSuccessApi($user);
            }
            return $this->loginSuccess($user,$password);
        } else {
            return $this->loginFailure($user);
        }
    }

    private function loginSuccessApi(user $user): array
    {
        $token = auth('api')->login($user, true);
        $data['user'] = new UserResource(auth('api')->user());
        $data['token'] = 'Bearer ' . $token;
        return $this->handleLoginResponse('success',$data);
    }

    private function loginSuccess(user $user,$password): array
    {
        $credentials['email'] = $user->email;
        $credentials['password'] = $password;
        if (Auth::attempt($credentials)){
            return $this->handleLoginResponse('success');
        }
    }

    private function loginFailure(user $user): array
    {
        $this->incrementLoginAttempts($user);
        if ($user->login_attempts == 3) {
            return $this->handleLoginResponse('max_attempts');
        } elseif ($user->login_attempts >= 4) {
            $this->blockAccount($user);
            return $this->handleLoginResponse('blocked');
        }
        return $this->handleLoginResponse('invalid_password');
    }

    private function handleLoginResponse($status,$data = null): array
    {
        return [
            'status' => $status,
            'data' => $data,
        ];
    }
}
