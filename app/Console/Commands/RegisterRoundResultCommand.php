<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\RoundResultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {                        
        try {
            
           $service
                ->setCartolaApiService($cartolaApiService)
                ->register();

            $this->info('Round results were registered successfully!');
            
            return RegisterRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;            
        }
    }
}