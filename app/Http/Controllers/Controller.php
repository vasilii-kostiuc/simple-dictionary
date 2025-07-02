<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Simple Dictionary API", version: "1.0.0")]
abstract class Controller
{
    //
}
