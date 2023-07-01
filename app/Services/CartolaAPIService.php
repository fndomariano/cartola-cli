<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CartolaAPIService 
{
    const CARTOLA_BASE_URL = 'https://api.cartola.globo.com';

    public function getLeagueData()
    {        
        try {

            $endpoint = self::CARTOLA_BASE_URL . '/auth/liga/cartolas-da-ruindade';

            $response = Http::withHeaders(['X-GLB-Token' => env('GBLID')])->get($endpoint);

            return $response;

        } catch (\Exception $e) {
            throw $e;
        }
    }
}