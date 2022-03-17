<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class UpdateRankingsCommand extends Command
{
    protected $signature = 'update:rankings';

    public function handle()
    {
        $ambassadors = User::ambassadors()->get();

        $progressBar = $this->output->createProgressBar($ambassadors->count());
        $progressBar->start();

        $ambassadors->each(function (User $user) use ($progressBar) {
            Redis::zadd('rankings', (int) $user->revenue, $user->name);

            $progressBar->advance();
        });

        $progressBar->finish();
    }
}
