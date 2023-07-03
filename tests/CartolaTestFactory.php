<?php 

namespace Tests;

use App\Models\Season;
use App\Models\RoundResult;
use App\Models\Team;


class CartolaTestFactory 
{
    public function configureSeason() : Season
    {        
        $season = Season::factory()->create();
        
        $teams = Team::factory()->count(5)->create();
        $teamsId = $teams->pluck('id')->toArray();

        $season->teams()->attach($teamsId);
        
        return $season;        
    }

    public function registerRoundResult($round, $teams)
    {
        $response = self::getLeagueResponse($teams);
        
        foreach ($response['times'] as $result) {
        
            $filteredTeam = $teams->filter(function ($team) use ($result) {
                return $team->cartola_id == $result['time_id'];
            })->values()->first();

            RoundResult::factory([
                'round' => $round,
                'score' => $result['pontos']['rodada'],
                'ranking' => $result['ranking']['rodada'],
                'team_id' => $filteredTeam->id
            ])->create();
        }
    }

    public static function getLeagueResponse($teams) 
    {
        $response = ['times' => []];
        
        foreach ($teams as $i => $team) {
            
            $response['times'][] = [
                'nome' => $team->name,
                'nome_cartola' => $team->owner,
                'time_id' => $team->cartola_id,
                'pontos' => [
                    'rodada' => (100 - 5 - $i),
                ],
                'ranking' => [
                    'rodada' => $i
                ]
            ];
        }

        return $response;
    }


    public static function getMarketStatusResponse(bool $isOpen = true) 
    {
        return [
            'rodada_atual' => 10,
            'status_mercado' => $isOpen ? 1 : 2
        ];            
    }
}
