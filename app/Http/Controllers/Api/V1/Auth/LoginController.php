<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $device = $request->userAgent() ?? '';
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));

        return new ApiResponseResource([
            'data' => [
                'access_token' => $user->createToken($device, expiresAt: $expiresAt)->plainTextToken,
                'user' => new UserResource($user),
            ],
        ])->response()->setStatusCode(Response::HTTP_OK);
    }
}
