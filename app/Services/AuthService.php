<?php

namespace App\Services;

use App\DTOs\Auth\AuthDTO;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function register(AuthDTO $dto): array
    {
        $user = $this->userRepository->create([
            'email'    => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        return $this->generateResponse($user);
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(AuthDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return $this->generateResponse($user);
    }

    protected function generateResponse($user): array
    {
        return [
            'user'  => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
