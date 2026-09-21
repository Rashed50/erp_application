<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountTypeResource;
use App\Http\Responses\ApiResponse;
use App\Models\AccountType;
use Illuminate\Http\JsonResponse;

class AccountTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(['account_types' => AccountTypeResource::collection(AccountType::query()->orderBy('id')->get())]);
    }
}
