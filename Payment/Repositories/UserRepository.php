<?php

namespace Payment\Repositories;

use Payment\Models\User;

readonly class UserRepository extends AbstractRepository implements UserRepositoryInterface
{

    private const USER_FILE = STORAGE_DIR . '/users.json';

    public function __construct()
    {
        parent::__construct(self::USER_FILE);
    }

    public function getUserById(int $user_id): ?array
    {
        return $this->getData($user_id);
    }

    public function saveUser(User $user): void
    {
        $this->setData($user->id, (array)$user);
    }
}