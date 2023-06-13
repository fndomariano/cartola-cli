<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CartolaAPIService;
use App\Services\RoundResultService;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register {--league=cartolas-da-ruindade} {--round=}';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service)
    {
        $leagueSlug = $this->option('league');
        $round = $this->option('round');

        try {

            if ($leagueSlug == '' || $leagueSlug === null)
                throw new \Exception("The option --league is required");

            if ($round == '' || $round === null)
                throw new \Exception("The option --round is required");

            $service
                ->setCartolaApiService($cartolaApiService)
                ->register($round, $leagueSlug);
            
            return RegisterRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;            
        }

    }
}