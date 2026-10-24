<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The signed-in user's own profile and password.
 */
class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()->load('roles')));
    }

    public function update(UpdateProfileRequest $request, FileStorageService $fileStorage): JsonResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email']));
        $user->image = $fileStorage->replace($user->image, $request->file('image'), false, 'users');

        $user->save();

        return ApiResponse::success(new UserResource($user->load('roles')), __('Profile updated successfully.'));
    }

    /**
     * Change the password and sign out every other device; the current
     * session stays signed in.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['password' => $request->validated('new_password')]);

        $user->tokens()->whereKeyNot($user->currentAccessToken()->getKey())->delete();

        return ApiResponse::success(message: __('Password changed successfully. Other devices have been signed out.'));
    }
}
