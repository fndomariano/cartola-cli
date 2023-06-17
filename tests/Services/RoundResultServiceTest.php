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
        $totalTeams = 5;
        $teams = Team::factory()->count($totalTeams)->create();

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