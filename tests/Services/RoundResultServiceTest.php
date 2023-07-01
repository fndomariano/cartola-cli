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

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn(CartolaTestFactory::getCartolaLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register();
            
        $this->assertDatabaseHas('round_result', ['round' => 1]);        
    }

    public function test_must_throw_not_found_exception() : void
    {
        $teams = Team::factory()->count(3)->make();        

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn(CartolaTestFactory::getCartolaLeagueResponse($teams));

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
    
}