<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Auth\ProfileResource;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function show(): ApiResponseResource
    {
        return new ApiResponseResource(['data' => new ProfileResource(Auth::user())]);
    }

    public function update(ProfileUpdateRequest $request): ApiResponseResource
    {
        $user = $this->userService->updateProfile(Auth::user(), $request->validated());

        return new ApiResponseResource(['data' => new ProfileResource($user)]);
    }
}
