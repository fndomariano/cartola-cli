<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(): void
    {
        $leagueSlug = $this->ask('Slug da liga', 'cartolas-da-ruindade');
        $yearSeason = $this->ask('Ano', date('Y'));
        $round = $this->ask('Rodada: ');

        (new \App\Services\RoundResultService)->removeRoundResult($leagueSlug, $yearSeason, $round);
    }
}