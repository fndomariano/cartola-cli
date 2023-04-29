<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\League;
use App\Models\Team;
use App\Models\Season;

class SeasonService 
{
    public function handle($year, $valueRound, $valueSubscription, $numberExemptPlayersRound)
    {   
        $data = $this->getLeagueData();

        $league = new League;
        $league->name = $data['liga']['nome'];
        $league->slug = $data['liga']['slug'];
        $league->cartola_id = $data['liga']['liga_id'];
        $league->save();

        $teamsId = [];

        foreach ($data['times'] as $t) {
            $team = new Team;
            $team->name = $t['nome'];
            $team->owner = $t['nome_cartola'];
            $team->cartola_id = $t['time_id'];
            $team->save();

            $teamsId[] = $team->id;
        }        

        $season = new Season;
        $season->year = $year;
        $season->value_round = $valueRound;
        $season->value_subscription = $valueSubscription;
        $season->number_exempt_players_round = $numberExemptPlayersRound;
        $season->league_id = $league->id;
        $season->save();

        $season->teams()->attach($teamsId);
    }


    private function getLeagueData() 
    {        
        $response =  Http::withHeaders(['X-GLB-Token' => env('GBLID')])
            ->get('https://api.cartola.globo.com/auth/liga/cartolas-da-ruindade');

        return $response;
    }
}