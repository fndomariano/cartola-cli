<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\RoundResult;
use App\Models\Team;

class RoundResultService 
{
    private CartolaAPIService $cartolaApiService;

    public function register($round, $leagueSlug) : void
    {
        DB::beginTransaction();

        try {
        
            $data = $this->cartolaApiService->getLeagueData($leagueSlug);

            foreach ($data['times'] as $result) {
                
                $team = Team::where('cartola_id', '=', $result['time_id'])->firstOrFail();

                $roundResult = new RoundResult;
                $roundResult->round = $round;
                $roundResult->score = $result['pontos']['rodada'];
                $roundResult->ranking = $result['ranking']['rodada'];
                $roundResult->team_id = $team->id;
                $roundResult->save();
            }        

            DB::commit();

        } catch(\Exception $e) {
            
            DB::rollBack();

            dd($e->getMessage());
        }
    }

    public function remove($leagueSlug, $yearSeason, $round) : void
    {
        $roundResults = RoundResult::query()
            ->select('round_result.id')
            ->join('subscription', 'subscription.team_id', '=', 'round_result.team_id')
            ->join('season', 'season.id', '=', 'subscription.season_id')
            ->join('league', 'league.id', '=', 'season.league_id')
            ->where('league.slug', '=', $leagueSlug)
            ->where('season.year', '=', $yearSeason)
            ->where('round_result.round', '=', $round)
            ->get();

        DB::beginTransaction();

        try {

            foreach ($roundResults as $roundResult) {
                $roundResult->delete();
            }

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollBack();

            dd($e->getMessage());
        }

    }

    public function setCartolaApiService(CartolaApiService $cartolaApiService) : self
    {
        $this->cartolaApiService = $cartolaApiService;
        return $this;
    }
    
}