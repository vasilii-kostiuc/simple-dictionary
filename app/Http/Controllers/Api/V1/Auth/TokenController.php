<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function validateToken(Request $request){
        return new ApiResponseResource(['message' => 'Token is valid']);
    }

}
