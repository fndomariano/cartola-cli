<?php 

namespace Tests\Services;

use App\Models\RoundResult;
use App\Models\Team;
use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Tests\CartolaTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RoundResultServiceTest extends TestCase 
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
    }

    public function tearDown() : void
    {
        parent::tearDown();
    }

    public function test_must_register_round_result() : void
    {
        $teams = Team::factory()->count(5)->create();

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $marketFakeResponse = CartolaTestFactory::getMarketStatusResponse();

        $cartolaApiService
            ->shouldReceive('getMarketStatus')
            ->once()
            ->andReturn($marketFakeResponse);

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn(CartolaTestFactory::getLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register();
            
        $this->assertDatabaseHas('round_result', ['round' => ($marketFakeResponse['rodada_atual'] - 1)]);
    }

    public function test_must_do_nothing_when_market_status_is_not_opened() : void 
    {
        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $marketFakeResponse = CartolaTestFactory::getMarketStatusResponse(false);

        $cartolaApiService
            ->shouldReceive('getMarketStatus')
            ->once()
            ->andReturn($marketFakeResponse);
        
        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->times(0);

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register();

        $this->assertDatabaseCount('round_result', 0);
    }

    public function test_must_throw_exception_when_already_exists_round_results() : void
    {
        $round = 9;
        
        $factory = new CartolaTestFactory;
        $season = $factory->configureSeason();
        $teams = $season->teams()->get();
        $factory->registerRoundResult($round, $teams);

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $cartolaApiService
            ->shouldReceive('getMarketStatus')
            ->once()
            ->andReturn(CartolaTestFactory::getMarketStatusResponse());

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->times(0);

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('The round result %s has been registered already', $round));

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register();
    }

    public function test_must_throw_not_found_exception() : void
    {
        $teams = Team::factory()->count(3)->make();

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $cartolaApiService
            ->shouldReceive('getMarketStatus')
            ->once()
            ->andReturn(CartolaTestFactory::getMarketStatusResponse());

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn(CartolaTestFactory::getLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register();
    }

    public function test_must_remove_round_results() : void
    {
        $round = 3;
        $factory = new CartolaTestFactory;        
        $season = $factory->configureSeason();
        $factory->registerRoundResult($round, $season->teams()->get());
         
        $roundResultService = new RoundResultService();
        $roundResultService->remove();
            
        $this->assertDatabaseMissing('round_result', ['round' => $round]);
    }

    public function test_must_throw_exception_when_remove_round_result_without_database_records() : void
    {        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('No results were found to round 0'));
         
        $roundResultService = new RoundResultService();
        $roundResultService->remove();
    } 
}