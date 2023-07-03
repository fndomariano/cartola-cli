<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CartolaAPIService 
{
    const CARTOLA_BASE_URL = 'https://api.cartola.globo.com';

    const MARKET_STATUS_OPENED = 1;

    public function getLeagueData()
    {        
        try {

            $endpoint = self::CARTOLA_BASE_URL . '/auth/liga/cartolas-da-ruindade';

            $response = Http::withHeaders(['X-GLB-Token' => env('GBLID')])->get($endpoint);
                        
            $this->validate($response->getStatusCode());
        
            return $response;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getMarketStatus()
    {
        try {

            $endpoint = self::CARTOLA_BASE_URL . '/mercado/status';

            $response = Http::get($endpoint);
                        
            $this->validate($response->getStatusCode());
        
            return $response;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function validate(int $statusCode) : void 
    {
        if ($statusCode != 200)
            throw new \Exception('There is some problem on Cartola API', $statusCode);
    }
}