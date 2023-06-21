<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CartolaAPIService 
{
    const CARTOLA_URL_AUTH_LEAGUE = 'https://api.cartola.globo.com/auth/liga/';

    public function getLeagueData($leagueSlug)
    {        
        try {
            $response = Http::withHeaders(['X-GLB-Token' => env('GBLID')])
                ->get(self::CARTOLA_URL_AUTH_LEAGUE . $leagueSlug);

            return $response;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}