<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\UserResource;
use App\Service\UserService;

class RegisterController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(RegisterUserRequest $request)
    {
        $user = $this->userService->register($request->get('email'), $request->get('password'), $request->get('name'));

        $device = $request->userAgent() ?? '';

        $accessToken = $user->createToken($device)->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'user' => new UserResource($user),
        ]);
    }
}
