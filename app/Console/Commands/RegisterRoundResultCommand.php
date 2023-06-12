<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CartolaAPIService;
use App\Services\RoundResultService;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service)
    {
        $leagueSlug = $this->ask('Slug da liga', 'cartolas-da-ruindade');
        $round = $this->ask('Rodada');

        $service
            ->setCartolaApiService($cartolaApiService)
            ->register($round, $leagueSlug);

        return RegisterRoundResultCommand::SUCCESS;
    }
}