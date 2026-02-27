<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService
{
    private string $secret;
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secret = config('services.wss.secret');

        if (empty($this->secret)) {
            throw new \RuntimeException('WSS_SERVICE_SECRET is not configured');
        }
    }

        public function generateServiceToken(string $serviceName, int $expiresInSeconds = 3600): string
    {
        $payload = [
            'iss' => config('app.name'), // Issuer
            'sub' => $serviceName, // Subject (service name)
            'iat' => time(), // Issued at
            'exp' => time() + $expiresInSeconds, // Expiration
            'type' => 'service',
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {

        }

        return null;
    }

    public function isValidServiceToken(string $token): bool
    {
        $decoded = $this->validateToken($token);

        return $decoded !== null &&
               isset($decoded->type) &&
               $decoded->type === 'service';
    }
}
