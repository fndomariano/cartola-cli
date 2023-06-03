<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeasonUpdateSubscriptionsCommand extends Command
{    
    protected $signature = 'season:update-subscriptions';
 
    protected $description = '';
 
    public function handle(): void
    {
        $leagueSlug = $this->ask('Slug da liga', 'cartolas-da-ruindade');
        $yearSeason = $this->ask('Ano', date('Y'));

        (new \App\Services\SeasonService)->updateSubscriptions($leagueSlug, $yearSeason);
    }
}