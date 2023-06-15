<?php 

namespace App\Console\Commands;

use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Illuminate\Console\Command;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove {--league=cartolas-da-ruindade} {--yearSeason=} {--round=}';
 
    protected $description = 'Remove teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {
        try {

            $leagueSlug = $this->option('league');
            $yearSeason = $this->option('yearSeason');
            $round = $this->option('round');

            $this->validateOptions($leagueSlug, $yearSeason, $round);            

            $service
                ->setCartolaApiService($cartolaApiService)
                ->remove($leagueSlug, $yearSeason, $round);

            return RemoveRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;
        }
    }

    public function validateOptions($leagueSlug, $yearSeason, $round) : void
    {
        if ($leagueSlug == '' || $leagueSlug === null)
            throw new \InvalidArgumentException("The option --league is required");

        if ($yearSeason == '' || $yearSeason === null)
            throw new \InvalidArgumentException("The option --yearSeason is required");

        if ($round == '' || $round === null)
            throw new \InvalidArgumentException("The option --round is required");
    }
}