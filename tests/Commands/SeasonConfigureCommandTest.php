<?php 

namespace Tests\Commands;

use App\Console\Commands\SeasonConfigureCommand;
use App\Models\RoundResult;
use App\Services\SeasonService;
use App\Services\CartolaAPIService;
use Mockery;
use Tests\TestCase;

class SeasonConfigureCommandTest extends TestCase
{
    private const COMMAND_NAME = 'season:configure';
    
    public function test_that_command_must_configure_season() : void
    {
        $leagueSlug = 'cartolas-da-ruindade';
        $yearSeason = date('Y');
        $valueRound = 3;
        $valueSubscription = 30;
        $numberExemptPlayersRound = 3;

        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $seasonService = Mockery::mock(SeasonService::class);  

        $seasonService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $seasonService
            ->shouldReceive('configure')
            ->with($leagueSlug, $yearSeason, $valueRound, $valueSubscription, $numberExemptPlayersRound)
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(SeasonService::class, $seasonService);

        $this->artisan(self::COMMAND_NAME)
            ->expectsQuestion('League slug', $leagueSlug)
            ->expectsQuestion('Season year', $yearSeason)
            ->expectsQuestion('Value by round', $valueRound)
            ->expectsQuestion('Subscription fee', $valueSubscription)
            ->expectsQuestion('Number of excempt players by round', $numberExemptPlayersRound)
            ->assertExitCode(SeasonConfigureCommand::SUCCESS);
    }
}