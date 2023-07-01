<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\SeasonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SeasonConfigureCommand extends Command
{    
    protected $signature = 'season:configure {--roundValue=3} {--subscriptionFee=30} {--numberExemptPlayersRound=3}';
 
    protected $description = 'Configure billing and quantity of excempt players by round';
 
    public function handle(CartolaAPIService $cartolaApiService, SeasonService $service) : int
    {
        $valueRound = (float) $this->option('roundValue');
        $subscriptionFee =  (float) $this->option('subscriptionFee');
        $numberExemptPlayersRound = (int) $this->option('numberExemptPlayersRound');

        try {

            $this->validate($valueRound, $subscriptionFee, $numberExemptPlayersRound);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->configure($valueRound, $subscriptionFee, $numberExemptPlayersRound);

            $this->info('Season was configured successfully!');

            return SeasonConfigureCommand::SUCCESS;
        
        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return SeasonConfigureCommand::INVALID;
        }
    }

    private function validate(float $valueRound, float $subscriptionFee, int $numberExemptPlayersRound) : void
    {
        $validator = Validator::make([
            'valueRound' => $valueRound,
            'subscriptionFee' => $subscriptionFee,
            'numberExemptPlayersRound' => $numberExemptPlayersRound
        ], [
            'valueRound' => 'numeric|min:1',
            'subscriptionFee' => 'numeric|min:1',
            'numberExemptPlayersRound' => 'numeric|min:1'
        ]);
        
        if ($validator->fails()) 
            throw new \Exception($validator->errors()->first());
    }
}