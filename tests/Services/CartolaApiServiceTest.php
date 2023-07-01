<?php 

namespace Tests\Services;

use App\Models\Team;
use App\Services\CartolaAPIService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\CartolaTestFactory;
use Tests\TestCase;

class CartolaApiServiceTest extends TestCase 
{
    public function test_must_return_response_from_cartola_api() : void 
    {
        $teams = Team::factory()->count(5)->make();
        
        $endpoint = CartolaAPIService::CARTOLA_BASE_URL . '/auth/liga/cartolas-da-ruindade';
        
        $fakeResponse = CartolaTestFactory::getCartolaLeagueResponse($teams);

        Http::fake([
            $endpoint => Http::response($fakeResponse)
        ]);
        
        $response = json_decode(Http::get($endpoint)->body(), true);
    
        Http::assertSent(function (Request $request) use ($response) {
            return array_key_exists('times', $response) 
                && !empty($response['times']) 
                && count($response['times']) == 5;
        });
    }

    public function test_must_return_bad_request_from_cartola_api() : void 
    {        
        $endpoint = CartolaAPIService::CARTOLA_BASE_URL . '/auth/liga/cartolas-da-ruindade';
        
        Http::fake([
            $endpoint => Http::response('Bad Request', 400)
        ]);
        
        $response = Http::get($endpoint);
        
        Http::assertSent(function (Request $request) use ($response) {
            return $response->getStatusCode() == 400;
        });
    }
}
