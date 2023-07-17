<?php 

namespace App\Console\Commands;

use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove';
 
    protected $description = 'Remove score of the last round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {
        try {

            $service
                ->setCartolaApiService($cartolaApiService)
                ->remove();

            $this->info('Round results were removed successfully!');

            return RemoveRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;
        }
    }
}