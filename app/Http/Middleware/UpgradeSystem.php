<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UpgradeSystem
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->needsReset()) {
            try {
                DB::purge('mysql');
                DB::reconnect('mysql');
                Log::info('Database connection has been reset.');
            } catch (\Exception $e) {
                Log::error('Failed to reset DB connection: ' . $e->getMessage());
            }

            try {
                Artisan::call('queue:restart');
                Log::info('Queue workers are being restarted.');
            } catch (\Exception $e) {
                Log::error('Failed to restart queue workers: ' . $e->getMessage());
            }

            try {
                exec('sudo systemctl restart redis', $output, $returnVar);
                Redis::client()->flushdb();
                Redis::client()->ping();   
                Log::info('Redis connection reset successfully.');
            } catch (\Exception $e) {
                Log::error('Failed to reset Redis connection: ' . $e->getMessage());
            }
        }

        return $next($request);
    }

    protected function needsReset()
    {
        try {
            DB::connection()->getPdo();
            if (Redis::ping() !== 'PONG') {
                return true;
            }
        } catch (\Exception $e) {
            return true;
        }
        return false;
    }
}
