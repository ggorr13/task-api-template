<?php

namespace App\Filters\Task;

use App\Filters\Task\interfaces\TaskFilterInterface;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter implements TaskFilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        return $query->where('status', $value);
    }
}
