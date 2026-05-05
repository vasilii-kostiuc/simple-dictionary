<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return new ApiResponseResource(['message' => 'Logged out successfully'])->response();
    }
}
