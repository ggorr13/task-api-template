<?php

namespace App\Filters\Task;

use App\Filters\Task\interfaces\TaskFilterInterface;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter implements TaskFilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        if (config('database.default') === 'sqlite') {
            return $query->where(fn($q) => $q->where('title', 'like', "%$value%")->orWhere('description', 'like', "%$value%"));
        }
        return $query->whereFullText(['title', 'description'], $value);
    }
}
