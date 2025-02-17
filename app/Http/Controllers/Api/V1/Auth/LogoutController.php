<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(){
        $user = Auth::user();
        $user->tokens()->delete();

        return response(['message' => 'Logged out successfully']);
    }
}
