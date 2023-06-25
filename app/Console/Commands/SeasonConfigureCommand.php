<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\SeasonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SeasonConfigureCommand extends Command
{    
    protected $signature = 'season:configure {--league=cartolas-da-ruindade} {--seasonYear=} {--roundValue=3} {--subscriptionFee=30} {--numberExemptPlayersRound=3}';
 
    protected $description = 'Configure league, billing and quantity of excempt players by round';
 
    public function handle(CartolaAPIService $cartolaApiService, SeasonService $service) : int
    {
        $leagueSlug = (string) $this->option('league');
        $seasonYear = (int) $this->option('seasonYear');
        $valueRound = (float) $this->option('roundValue');
        $subscriptionFee =  (float) $this->option('subscriptionFee');
        $numberExemptPlayersRound = (int) $this->option('numberExemptPlayersRound');

        try {

            $this->validate($leagueSlug, $seasonYear, $valueRound, $subscriptionFee, $numberExemptPlayersRound);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->configure($leagueSlug, $seasonYear, $valueRound, $subscriptionFee, $numberExemptPlayersRound);

            $this->info('Season was configured successfully!');

            return SeasonConfigureCommand::SUCCESS;
        
        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return SeasonConfigureCommand::INVALID;
        }
    }

    private function validate(string $leagueSlug, int $seasonYear, float $valueRound, float $subscriptionFee, int $numberExemptPlayersRound) : void
    {
        $validator = Validator::make([
            'league' => $leagueSlug,
            'seasonYear' => $seasonYear,
            'valueRound' => $valueRound,
            'subscriptionFee' => $subscriptionFee,
            'numberExemptPlayersRound' => $numberExemptPlayersRound
        ], [
            'league' => 'required',
            'seasonYear'  => 'numeric|min:1',
            'valueRound' => 'numeric|min:1',
            'subscriptionFee' => 'numeric|min:1',
            'numberExemptPlayersRound' => 'numeric|min:1'
        ]);
        
        if ($validator->fails()) 
            throw new \Exception($validator->errors()->first());
    }
}