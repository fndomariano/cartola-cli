<?php 

namespace App\Console\Commands;

use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Illuminate\Console\Command;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove';
 
    protected $description = 'Remove teams scores by round';
 
    public function handle(): void
    {
        $leagueSlug = $this->ask('Slug da liga', 'cartolas-da-ruindade');
        $yearSeason = $this->ask('Ano', date('Y'));
        $round = $this->ask('Rodada');

        $service = new RoundResultService(new CartolaAPIService());
        $service->remove($leagueSlug, $yearSeason, $round);

        RemoveRoundResultCommand::SUCCESS;
    }
}