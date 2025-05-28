<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => $this->resource['success'] ?? true,
            'error' => $this->resource['error'] ?? null,
            'message' => $this->resource['message'] ?? null,
            'data' => $this->resource['data'] ?? null,
        ];
    }
}
