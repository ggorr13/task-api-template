<?php

namespace App\Filters\Task\interfaces;

use Illuminate\Database\Eloquent\Builder;

interface TaskFilterInterface
{
    public function apply(Builder $query, mixed $value): Builder;
}
