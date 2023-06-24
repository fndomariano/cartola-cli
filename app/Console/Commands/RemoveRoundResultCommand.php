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
            $yearSeason = (int) $this->option('yearSeason');
            $round = (int) $this->option('round');

            $this->validateOptions($leagueSlug, $yearSeason, $round);            

            $service
                ->setCartolaApiService($cartolaApiService)
                ->remove($leagueSlug, $yearSeason, $round);

            $this->info('Round result was removed successfully!');

            return RemoveRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;
        }
    }

    public function validateOptions(string $leagueSlug, int $yearSeason, int $round) : void
    {
        if ($leagueSlug === '' || $leagueSlug == null)
            throw new \InvalidArgumentException("The option --league is required");

        if ($yearSeason == '' || $yearSeason == null || $yearSeason <= 0)
            throw new \InvalidArgumentException("The option --yearSeason is required");

        if ($round == '' || $round == null || $round <= 0)
            throw new \InvalidArgumentException("The option --round is required");
    }
}