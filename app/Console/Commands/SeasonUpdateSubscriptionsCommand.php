<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\SeasonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SeasonUpdateSubscriptionsCommand extends Command
{    
    protected $signature = 'season:update-subscriptions';
 
    protected $description = 'Add and remove subscribed teams';
 
    public function handle(CartolaAPIService $cartolaApiService, SeasonService $service): int
    {
        try {

            $service
                ->setCartolaApiService($cartolaApiService)
                ->updateSubscriptions();

            $this->info('Season subscriptions have been updated successfully!');

            return SeasonUpdateSubscriptionsCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return SeasonUpdateSubscriptionsCommand::INVALID;
        }
    }    
}