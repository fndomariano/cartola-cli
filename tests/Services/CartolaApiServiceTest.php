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
    private const ENDPOINT_AUTH_LEAGUE = CartolaAPIService::CARTOLA_BASE_URL . '/auth/liga/cartolas-da-ruindade';
    private const ENDPOINT_MARKET_STATUS = CartolaAPIService::CARTOLA_BASE_URL . '/mercado/status';

    public function test_must_return_response_when_request_league_data() : void 
    {
        $teams = Team::factory()->count(5)->make();
        
        $fakeResponse = CartolaTestFactory::getLeagueResponse($teams);

        Http::fake([
            self::ENDPOINT_AUTH_LEAGUE => Http::response($fakeResponse)
        ]);        
        
        $service = new CartolaAPIService();        
        $response = json_decode($service->getLeagueData()->body(), true);
            
        Http::assertSent(function (Request $request) use ($response) {
            return array_key_exists('times', $response) 
                && !empty($response['times']) 
                && count($response['times']) == 5;
        });
    }


    public function test_must_return_response_when_request_market_status() : void 
    {            
        $fakeResponse = CartolaTestFactory::getMarketStatusResponse();

        Http::fake([
            self::ENDPOINT_MARKET_STATUS => Http::response($fakeResponse)
        ]);        
        
        $service = new CartolaAPIService();
        $response = json_decode($service->getMarketStatus()->body(), true);
        
        Http::assertSent(function (Request $request) use ($response) {
            return array_key_exists('rodada_atual', $response)
                && array_key_exists('status_mercado', $response) 
                && $response['rodada_atual'] === 10
                && $response['status_mercado'] === CartolaAPIService::MARKET_STATUS_OPENED;
        });
    }

    public function test_must_return_bad_request_when_request_league_data() : void 
    {                
        Http::fake([
            self::ENDPOINT_AUTH_LEAGUE => Http::response('', 400)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There is some problem on Cartola API');
        $this->expectExceptionCode(400);

        $service = new CartolaAPIService();
        $service->getLeagueData();                
    }

    public function test_must_return_bad_request_when_request_market_status() : void 
    {                
        Http::fake([
            self::ENDPOINT_MARKET_STATUS => Http::response('', 400)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There is some problem on Cartola API');
        $this->expectExceptionCode(400);

        $service = new CartolaAPIService();
        $service->getMarketStatus();                
    }
}
