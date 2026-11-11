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
     * The token dies after `sanctum.idle_timeout` minutes without a request.
     *
     * @return array{user: User, token: string, idle_timeout: int}
     */
    public function login(string $email, string $password, string $deviceName = 'api'): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        /** @var NewAccessToken $accessToken */
        $accessToken = $user->createToken($deviceName);

        return [
            'user' => $user->load('roles'),
            'token' => $accessToken->plainTextToken,
            'idle_timeout' => (int) config('sanctum.idle_timeout'),
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
