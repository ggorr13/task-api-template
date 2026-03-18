<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'access_token' => $this->resource['token'],
            'token_type'   => 'Bearer',
            // 'user'      => new UserResource($this->resource['user']) // If necessary
        ];
    }
}
