<?php 

namespace App\Console\Commands;

use App\Services\CartolaAPIService;
use App\Services\RoundResultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register {--league=cartolas-da-ruindade} {--round=}';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {                        
        try {
            
            $leagueSlug = (string) $this->option('league');
            $round = (int) $this->option('round');            
            
            $this->validate($leagueSlug, $round);

            $service
                ->setCartolaApiService($cartolaApiService)
                ->register($round, $leagueSlug);

            $this->info('Round result was registered successfully!');
            
            return RegisterRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;            
        }
    }

    private function validate(string $leagueSlug, int $round) : void
    {
        $validator = Validator::make([
            'league' => $leagueSlug,
            'round' => $round
        ], [
            'league' => 'required',
            'round'  => 'numeric|min:1|max:38'
        ]);
        
        if ($validator->fails()) 
            throw new \Exception($validator->errors()->first());
    }
}