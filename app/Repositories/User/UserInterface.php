<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserInterface
{
    public function create(array $attributes): User;

    public function update(int $id, array $attributes): bool;

    public function find(int $id): User;
}
