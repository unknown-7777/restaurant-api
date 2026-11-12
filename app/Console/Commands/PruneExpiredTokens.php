<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class PruneExpiredTokens extends Command
{
    protected $signature   = 'tokens:prune';
    protected $description = 'Delete expired Sanctum tokens';

    public function handle(): void
    {
        $deleted = PersonalAccessToken::where('expires_at', '<', now())->delete();
        $this->info("Deleted {$deleted} expired tokens.");
    }
}
