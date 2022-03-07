<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserInterface
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(array $attributes): User
    {
        $attributes['password'] = bcrypt($attributes['password']);
        return $this->user->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {
        return (bool)$this->user->whereId($id)->update($attributes);
    }

    public function find(int $id): User
    {
        return $this->user->whereId($id)->first();
    }

    public function findBy(string $column_name, string $value): ?User
    {
        return $this->user->where($column_name, $value)->first();
    }
}
