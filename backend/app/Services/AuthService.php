<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Attempt to authenticate a user by credentials and issue a new API token.
     *
     * @return array{user: User, token: string}
     */
    public function login(string $email, string $password, string $deviceName = 'api'): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var NewAccessToken $accessToken */
        $accessToken = $user->createToken($deviceName);

        return [
            'user' => $user->load('roles'),
            'token' => $accessToken->plainTextToken,
        ];
    }

    /**
     * Revoke the access token used to authenticate the current request.
     */
    public function logout(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        $token?->delete();
    }
}
