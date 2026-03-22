<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'status'     => 'required|string|in:todo,in-progress,done',
            'description'=> 'nullable|string',
            'due_date'   => 'nullable|date',
        ];
    }
}
