<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SeasonService 
{
    public function configure()
    {
        $response =  Http::withHeaders(['X-GLB-Token' => env('GBLID')])
            ->get('https://api.cartola.globo.com/auth/liga/cartolas-da-ruindade');

        dd($response['time_dono']);
    }
}