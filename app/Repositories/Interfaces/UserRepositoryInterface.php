<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?Model;
    public function create(array $data): Model;
}
