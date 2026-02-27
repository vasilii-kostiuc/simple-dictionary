<?php

namespace App\Console\Commands;

use App\Service\JwtTokenService;
use Illuminate\Console\Command;

class GenerateServiceToken extends Command
{
    protected $signature = 'service:generate-token {service=wss-server} {--ttl=31536000 : Token TTL in seconds (default: 1 year)}';

    protected $description = 'Generate JWT token for service-to-service authentication';

    public function handle(JwtTokenService $tokenService)
    {
        $serviceName = $this->argument('service');
        $ttl = (int) $this->option('ttl');

        $token = $tokenService->generateServiceToken($serviceName, $ttl);

        $this->info("JWT token generated for service: {$serviceName}");
        $this->newLine();
        $this->line("Token (valid for {$ttl} seconds):");
        $this->line($token);
        $this->newLine();
        $this->warn('Add this token to your WSS service environment as WSS_API_TOKEN');

        return 0;
    }
}
