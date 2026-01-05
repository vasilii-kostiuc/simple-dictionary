<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateWssToken extends Command
{
    protected $signature = 'wss:generate-token {--force : Force regenerate token even if it exists}';

    protected $description = 'Generate API token for WSS server';

    public function handle()
    {
        // Найти или создать служебного пользователя
        $user = User::firstOrCreate(
            ['email' => 'wss-server@system.local'],
            [
                'name' => 'WSS Server',
                'password' => bcrypt(Str::random(32)),
            ]
        );

        // Проверить существующий токен
        $existingToken = $user->tokens()
            ->where('name', 'wss-server-token')
            ->first();

        if ($existingToken && !$this->option('force')) {
            $this->warn('WSS token already exists!');
            $this->info('Use --force flag to regenerate the token');
            $this->newLine();
            $this->line('Existing token created at: ' . $existingToken->created_at);
            return 1;
        }

        if ($existingToken && $this->option('force')) {
            $this->warn('Regenerating token...');
            $user->tokens()->where('name', 'wss-server-token')->delete();
        }

        // Создать новый токен
        $token = $user->createToken('wss-server-token', [
            'training:expire',
            'training:manage',
            // Добавьте другие abilities по необходимости
        ])->plainTextToken;

        $this->info('✓ WSS Server Token generated successfully:');
        $this->newLine();
        $this->line($token);
        $this->newLine();
        $this->info('Add this to your WSS project .env file:');
        $this->line("API_WSS_SERVER_TOKEN={$token}");
        $this->newLine();

        return 0;
    }
}
