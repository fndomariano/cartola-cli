<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\League;
use App\Models\Team;
use App\Models\Season;
use App\Models\RoundResult;

class SeasonService 
{
    public function configure($year, $valueRound, $valueSubscription, $numberExemptPlayersRound) : void
    {   
        DB::beginTransaction();
        
        try {

            $data = $this->getLeagueData();

            $leagueId = $this->createLeague($data['liga']);
            
            $teamsId = $this->createTeams($data['times']);

            $season = $this->createSeason($year, $valueRound, $valueSubscription, $numberExemptPlayersRound, $leagueId, $teamsId);

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollBack();

            dd($e->getMessage());
        }      
    }

    public function updateSubscriptions(string $leagueSlug, int $seasonYear) : void
    {
        DB::beginTransaction();

        try {
            $data = $this->getLeagueData();

            $league = League::where('slug', '=', $leagueSlug)->firstOrFail();

            $season = Season::where('league_id', '=', $league->id)
                ->where('year', '=', $seasonYear)
                ->firstOrFail();
                            
            $teamsId = $this->createTeams($data['times']);

            $season->teams()->attach($teamsId);

            $this->addRoundResult($teamsId, $season);

            DB::commit();
        
        } catch (\Exception $e) {

            DB::rollBack();

            dd($e->getMessage());
        }
        
    }

    private function createSeason($year, $valueRound, $valueSubscription, $numberExemptPlayersRound, $leagueId, $teamsId)
    {     
        $season = new Season;
        $season->year = $year;
        $season->value_round = $valueRound;
        $season->value_subscription = $valueSubscription;
        $season->number_exempt_players_round = $numberExemptPlayersRound;
        $season->league_id = $league->id;
        $season->save();

        $season->teams()->attach($teamsId);
    }

    private function createLeague($data) 
    {
        $league = new League;
        $league->name = $data['nome'];
        $league->slug = $data['slug'];
        $league->cartola_id = $data['liga_id'];
        $league->save();

        return $league->id;
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

    private function getLeagueData() 
    {        
        $response = Http::withHeaders(['X-GLB-Token' => env('GBLID')])
            ->get('https://api.cartola.globo.com/auth/liga/cartolas-da-ruindade');

        return $response;
    }
}