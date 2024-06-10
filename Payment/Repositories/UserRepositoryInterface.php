<?php

namespace Payment\Repositories;

use Payment\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{

    public function getUserById(string $user_id): ?array;

    public function saveUser(User $user): void;

}