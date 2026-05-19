<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use VasiliiKostiuc\PubSubBroker\Messaging\RedisBroker;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Redis::publish('test-channel', json_encode([
            'name' => 'Adam Wathan',
        ]));
        new RedisBroker()->publish('test-channel', 'Hello World');
        Redis::subscribe(['test-channel'], function ($message, $channel) {
            dump($channel);
            dump($message);
        });
    }
}
