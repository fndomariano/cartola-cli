<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\SeasonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SeasonUpdateSubscriptionsCommand extends Command
{    
    protected $signature = 'season:update-subscriptions {--league=cartolas-da-ruindade} {--seasonYear=}';
 
    protected $description = 'Add and remove subscribed teams';
 
    public function handle(CartolaAPIService $cartolaApiService, SeasonService $service): int
    {
        $leagueSlug = (string) $this->option('league');
        $seasonYear = (int) $this->option('seasonYear');

        try {

            $this->validate($leagueSlug, $seasonYear);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->updateSubscriptions($leagueSlug, $seasonYear);

            $this->info('Season subscriptions has updated successfully!');

            return SeasonUpdateSubscriptionsCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return SeasonUpdateSubscriptionsCommand::INVALID;
        }
    }

    private function validate(string $leagueSlug, int $seasonYear) : void
    {
        $validator = Validator::make([
            'league' => $leagueSlug,
            'seasonYear' => $seasonYear
        ], [
            'league' => 'required',
            'seasonYear'  => 'numeric|min:1'
        ]);
        
        if ($validator->fails()) 
            throw new \Exception($validator->errors()->first());
    }
}