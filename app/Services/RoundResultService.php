<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\RoundResult;
use App\Models\Team;

class RoundResultService 
{
    public function handle($round)
    {
        $data = $this->getLeagueData();

        foreach ($data['times'] as $result) {
            
            $team = Team::where('cartola_id', '=', $result['time_id'])->firstOrFail();

            $roundResult = new RoundResult;
            $roundResult->round = $round;
            $roundResult->score = $result['pontos']['rodada'];
            $roundResult->ranking = $result['ranking']['rodada'];
            $roundResult->team_id = $team->id;
            $roundResult->save();
        }        
    }

    private function getLeagueData() 
    {        
        $response =  Http::withHeaders(['X-GLB-Token' => env('GBLID')])
            ->get('https://api.cartola.globo.com/auth/liga/cartolas-da-ruindade');

        return $response;
    }
}