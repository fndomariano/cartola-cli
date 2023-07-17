<?php 

namespace Tests\Services;

use App\Models\Team;
use App\Services\SeasonService;
use App\Services\CartolaAPIService;
use Tests\CartolaTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SeasonServiceTest extends TestCase 
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

    public function test_must_configure_season() : void
    {
        $valueRound = 2.0;
        $subscriptionFee = 20.0; 
        $numberExemptPlayersRound = 4;

        $teams = Team::factory()->count(5)->make();
        
        $cartolaApiService = Mockery::mock(CartolaAPIService::class);

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn(CartolaTestFactory::getLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $seasonService = new SeasonService();
        $seasonService
            ->setCartolaApiService($cartolaApiService)
            ->configure($valueRound, $subscriptionFee, $numberExemptPlayersRound);

        
        $this->assertDatabaseHas('season', [
            'year' => date('Y'),
            'value_round' => $valueRound,
            'value_subscription' => $subscriptionFee,
            'number_exempt_players_round' => $numberExemptPlayersRound
        ]);

        $this->assertDatabaseCount('season', 1);
        $this->assertDatabaseCount('team', 5);
        $this->assertDatabaseCount('subscription', 5);
    }

    public function test_must_update_season_subscriptions() : void
    {           
        $factory = new CartolaTestFactory;        
        
        $season = $factory->configureSeason();        
        $teams = $season->teams()->get();
        
        $factory->registerRoundResult(1, $teams);
        $factory->registerRoundResult(2, $teams);

        $newTeam = Team::factory()->make();
        $responseNewTeam = CartolaTestFactory::getLeagueResponse([$newTeam]);
        
        $cartolaApiService = Mockery::mock(CartolaAPIService::class);

        $response = CartolaTestFactory::getLeagueResponse($teams);
        $response['times'][] = $responseNewTeam['times'][0];

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->once()
            ->andReturn($response);

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $seasonService = new SeasonService();
        $seasonService
            ->setCartolaApiService($cartolaApiService)
            ->updateSubscriptions();
        
        $this->assertDatabaseCount('team', 6);
        $this->assertDatabaseCount('subscription', 6);
        $this->assertDatabaseCount('round_result', 12);
        $this->assertDatabaseHas('round_result', [
            'ranking' => 6,
            'score' => 0,
            'round' => 2            
        ]);
        $this->assertDatabaseMissing('round_result', ['round' => 3]);
    }
}