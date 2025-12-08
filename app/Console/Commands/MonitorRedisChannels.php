<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class MonitorRedisChannels extends Command
{
    protected $signature = 'redis:monitor {pattern=*}';
    protected $description = 'Monitor Redis pub/sub messages';

    public function handle(): void
    {
        $pattern = $this->argument('pattern');

        $this->info("Monitoring Redis channels: {$pattern}");
        $this->info("Press Ctrl+C to stop\n");

        Redis::psubscribe([$pattern], function (string $message, string $channel) {
            $timestamp = now()->format('H:i:s');

            $this->line("─────────────────────────────────────────");
            $this->info("⏰ [{$timestamp}] Channel: {$channel}");

            $decoded = json_decode($message, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->line(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $this->line($message);
            }
        });
    }
}
