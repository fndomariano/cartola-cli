<?php 

namespace App\Console\Commands;

use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Illuminate\Console\Command;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove {--league=cartolas-da-ruindade} {--yearSeason=} {--round=}';
 
    protected $description = 'Remove teams scores by round';
 
    public function handle(): void
    {
        $leagueSlug = $this->option('league');
        $yearSeason = $this->option('yearSeason');
        $round = $this->option('round');

        $service = new RoundResultService(new CartolaAPIService());
        $service->remove($leagueSlug, $yearSeason, $round);

        RemoveRoundResultCommand::SUCCESS;
    }
}