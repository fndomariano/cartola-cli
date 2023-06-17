<?php 

namespace Tests\Services;


use App\Models\RoundResult;
use App\Models\Team;
use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
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

        $round = 2;
        $leagueSlug = 'cartolas-da-ruindade';

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->with($leagueSlug)
            ->once()
            ->andReturn($this->getCartolaLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register($round, $leagueSlug);
            
        $this->assertDatabaseHas('round_result', ['round' => $round]);        
    }

    public function test_must_throw_not_found_exception() : void
    {
        $teams = Team::factory()->count(3)->make();
        $round = 2;
        $leagueSlug = 'cartolas-da-ruindade';

        $cartolaApiService = Mockery::mock(CartolaAPIService::class);  

        $cartolaApiService
            ->shouldReceive('getLeagueData')
            ->with($leagueSlug)
            ->once()
            ->andReturn($this->getCartolaLeagueResponse($teams));

        $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->register($round, $leagueSlug);
    }

    public function test_must_remove_round_results() : void
    {
        $round = 2;
        $leagueSlug = 'cartolas-da-ruindade';
        $teams = Team::factory()->count(5)->create();
        
        foreach ($teams as $team) {
            RoundResult::factory([
                'team_id' => $team->id
            ])->create();
        }

        $roundResultService = new RoundResultService();
        $roundResultService
            ->setCartolaApiService($cartolaApiService)
            ->remove($round, $leagueSlug);
            
        $this->assertDatabaseHas('round_result', ['round' => $round]);
    }


    public function getCartolaLeagueResponse($teams) 
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
}