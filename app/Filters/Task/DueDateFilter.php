<?php

namespace App\Filters\Task;

use App\Filters\Task\interfaces\TaskFilterInterface;
use Illuminate\Database\Eloquent\Builder;

class DueDateFilter implements TaskFilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        return $query->whereDate('due_date', $value);
    }
}
