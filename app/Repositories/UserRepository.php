<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Collection;

interface UserRepository
{
    public function getByEmail(string $email): ?User;

    public function getById(int $id): ?User;

    public function update(User $user): bool;

    public function create(array $data): User;

    public function getAllOrderedByIndexes(): Collection;

    public function isAccountBlocked(User $user): bool;

    public function blockAccount(User $user): bool;

    public function resetLoginAttempts(User $user): bool;

    public function incrementLoginAttempts(User $user): bool;

    public function verifyPassword(string $password, User $user): bool;

    public function generateUserToken(): string;

    public function createUserToken(User $user): void;

    public function getUserTokenCount(User $user):int;

    public function isMaxDevicesReached(User $user): bool;

    public function processLogin(User $user, string $password): array;
}
