<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\SeasonService;
use Illuminate\Console\Command;

class SeasonConfigureCommand extends Command
{    
    protected $signature = 'season:configure';
 
    protected $description = 'Configure league, billing and quantity of excempt players by round';
 
    public function handle(CartolaAPIService $cartolaApiService, SeasonService $service) : int
    {
        $leagueSlug = $this->ask('League slug', 'cartolas-da-ruindade');
        $year = $this->ask('Season year', date('Y'));
        $valueRound = $this->ask('Value by round', 3);
        $valueSubscription = $this->ask('Subscription fee', 30);
        $numberExemptPlayersRound = $this->ask('Number of excempt players by round', 3);

        try {

            $this->validateOptions($leagueSlug, $year, $valueRound, $valueSubscription, $numberExemptPlayersRound);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->configure($leagueSlug, $year, $valueRound, $valueSubscription, $numberExemptPlayersRound);

            $this->info('Season was configured successfully');

            return SeasonConfigureCommand::SUCCESS;
        
        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return SeasonConfigureCommand::INVALID;
        }
    }

    private function validateOptions(string $leagueSlug, int $year, float $valueRound, float $valueSubscription, int $numberExemptPlayersRound) : void
    {
        if ($leagueSlug == '' || $leagueSlug === null)
            throw new \InvalidArgumentException("The League Slug is required");

        if ($year == '' || $year === null || $year <= 0)
            throw new \InvalidArgumentException("The Season Year is required");
        
        if ($valueRound == '' || $valueRound === null || $valueRound <= 0)
            throw new \InvalidArgumentException("The Value Round is required");
        
        if ($valueSubscription == '' || $valueSubscription === null || $valueRound <= 0)
            throw new \InvalidArgumentException("The Subscription Fee is required");
        
        if ($numberExemptPlayersRound == '' || $numberExemptPlayersRound === null || $numberExemptPlayersRound <= 0)
            throw new \InvalidArgumentException("The Number of Excempt Players is required");
    }
}