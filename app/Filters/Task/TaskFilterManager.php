<?php

namespace App\Filters\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class TaskFilterManager
{
    protected array $filters = [
        'status' => StatusFilter::class,
        'search' => SearchFilter::class,
        'due_date' => DueDateFilter::class,
    ];

    public function apply(Builder $query, array $data): Builder
    {
        foreach ($data as $key => $value) {
            if (isset($this->filters[$key]) && !empty($value)) {
                $strategy = App::make($this->filters[$key]);
                $query = $strategy->apply($query, $value);
            }
        }
        return $query;
    }
}
