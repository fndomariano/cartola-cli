<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CartolaAPIService;
use App\Services\RoundResultService;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register {--league=cartolas-da-ruindade} {--round=}';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {                        
        try {
            
            $leagueSlug = $this->option('league');
            $round = $this->option('round');

            $this->validateOptions($leagueSlug, $round);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->register($round, $leagueSlug);
            
            return RegisterRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;            
        }
    }

    private function validateOptions($leagueSlug, $round) : void
    {
        if ($leagueSlug == '' || $leagueSlug === null)
            throw new \InvalidArgumentException("The option --league is required");

        if ($round == '' || $round === null)
            throw new \InvalidArgumentException("The option --round is required");
    }
}