<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\{LoginRequest, RegisterRequest};
use App\DTOs\Auth\AuthDTO;
use App\Services\AuthService;
use App\Http\Resources\AuthResource;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): AuthResource
    {
        return new AuthResource(
            $this->authService->register(AuthDTO::fromRequest($request))
        );
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): AuthResource
    {
        return new AuthResource(
            $this->authService->authenticate(AuthDTO::fromRequest($request))
        );
    }
}
