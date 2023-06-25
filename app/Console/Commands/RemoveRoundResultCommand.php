<?php 

namespace App\Console\Commands;

use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RemoveRoundResultCommand extends Command
{    
    protected $signature = 'round-result:remove {--league=cartolas-da-ruindade} {--seasonYear=} {--round=}';
 
    protected $description = 'Remove teams scores by round';
 
    public function handle(CartolaAPIService $cartolaApiService, RoundResultService $service) : int
    {
        try {

            $leagueSlug = (string) $this->option('league');
            $seasonYear = (int) $this->option('seasonYear');
            $round = (int) $this->option('round');

            $this->validate($leagueSlug, $seasonYear, $round);            

            $service
                ->setCartolaApiService($cartolaApiService)
                ->remove($leagueSlug, $seasonYear, $round);

            $this->info('Round result was removed successfully!');

            return RemoveRoundResultCommand::SUCCESS;

        } catch (\Exception $e) {
            
            $this->error($e->getMessage());

            return RegisterRoundResultCommand::INVALID;
        }
    }

    private function validate(string $leagueSlug, int $seasonYear, int $round) : void
    {
        $validator = Validator::make([
            'league' => $leagueSlug,
            'seasonYear' => $seasonYear,
            'round' => $round
        ], [
            'league' => 'required',
            'seasonYear' => 'numeric|min:1',
            'round'  => 'numeric|min:1|max:38'
        ]);
        
        if ($validator->fails()) 
            throw new \Exception($validator->errors()->first());
    }
}