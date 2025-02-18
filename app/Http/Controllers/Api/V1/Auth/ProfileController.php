<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService){

        $this->userService = $userService;
    }
    public function show(User $user)
    {
        return new ProfileResource($user);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $this->userService->updateProfile($user, $request->validated());

        return new ProfileResource($user);
    }

}
