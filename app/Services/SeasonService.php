<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Team;
use App\Models\Season;
use App\Models\RoundResult;

class SeasonService 
{
    private CartolaAPIService $cartolaApiService;

    public function configure(float $valueRound, float $subscriptionFee, int $numberExemptPlayersRound) : void
    {   
        DB::beginTransaction();
        
        try {

            $data = $this->cartolaApiService->getLeagueData();
                        
            $teamsId = $this->createTeams($data['times']);

            $season = $this->createSeason($valueRound, $subscriptionFee, $numberExemptPlayersRound, $teamsId);

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollBack();

            throw $e;
        }      
    }

    public function updateSubscriptions() : void
    {
        DB::beginTransaction();

        try {
            $data = $this->getLeagueData();

            $season = Season::where('year', '=', (int) date('Y'))->firstOrFail();
                            
            $teamsId = $this->createTeams($data['times']);

            $season->teams()->attach($teamsId);

            $this->addRoundResult($teamsId, $season);

            DB::commit();
        
        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public function setCartolaApiService(CartolaApiService $cartolaApiService) : self
    {
        $this->cartolaApiService = $cartolaApiService;
        return $this;
    }

    private function createSeason($valueRound, $subscriptionFee, $numberExemptPlayersRound, $teamsId)
    {     
        $season = new Season;
        $season->year = (int) date('Y');
        $season->value_round = $valueRound;
        $season->value_subscription = $subscriptionFee;
        $season->number_exempt_players_round = $numberExemptPlayersRound;
        $season->save();

        $season->teams()->attach($teamsId);
    }

    private function createTeams($teams)
    {
        $teamsId = [];

        foreach ($teams as $t) {

            $doesNotExistsTeam = Team::where('cartola_id', '=', $t['time_id'])->doesntExist();

            if ($doesNotExistsTeam) {
                $team = new Team;
                $team->name = $t['nome'];
                $team->owner = $t['nome_cartola'];
                $team->cartola_id = $t['time_id'];
                $team->save();

                $teamsId[] = $team->id;                
            }

            unset($team);
        }

        return $teamsId;
    }

    private function addRoundResult($teamsId, $season) 
    {
        $roundResults = RoundResult::query()
            ->selectRaw('MAX(round_result.round) AS last_round, MAX(round_result.ranking) AS last_raking')
            ->join('subscription', 'subscription.team_id', '=', 'round_result.team_id')
            ->join('season', 'season.id', '=', 'subscription.season_id')
            ->where('season.id', '=', $season->id)
            ->first();        
        
        $ranking = $roundResults->last_raking + 1;
        
        foreach ($teamsId as $teamId) {
            
            for ($i = 1; $i <= $roundResults->last_round; $i++) {

                $roundResult = new RoundResult;
                $roundResult->round = $i;
                $roundResult->score = 0;
                $roundResult->ranking = $ranking;
                $roundResult->team_id = $teamId;
                $roundResult->save();
                                
                unset($roundResult);
            }

            $ranking++;
        }        
    }
}