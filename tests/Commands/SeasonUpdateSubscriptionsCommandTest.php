<?php 

namespace Tests\Commands;

use App\Console\Commands\SeasonUpdateSubscriptionsCommand;
use App\Models\RoundResult;
use App\Services\SeasonService;
use App\Services\CartolaAPIService;
use Mockery;
use Tests\TestCase;

class SeasonUpdateSubscriptionsCommandTest extends TestCase
{
    private const COMMAND_NAME = 'season:update-subscriptions';

    private const LEAGUE_OPTION = '--league';
    private const SEASON_YEAR_OPTION = '--seasonYear';
    

    public function test_that_command_must_update_subscriptions() : void
    {
        $leagueSlug = 'cartolas-da-ruindade';
        $seasonYear = 2023;

        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $seasonService = Mockery::mock(SeasonService::class);  

        $seasonService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $seasonService
            ->shouldReceive('updateSubscriptions')
            ->with($leagueSlug, $seasonYear)
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(SeasonService::class, $seasonService);

        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::SEASON_YEAR_OPTION, $seasonYear);
        
        $this->artisan($command)
            ->expectsOutput('Season subscriptions has updated successfully!')
            ->assertExitCode(SeasonUpdateSubscriptionsCommand::SUCCESS);
    }

    public function test_that_command_must_validate_league_option_required()
    {        
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, null);

        $this->artisan($command)
            ->expectsOutput('The league field is required.')
            ->assertExitCode(SeasonUpdateSubscriptionsCommand::INVALID);
    }

    public function test_that_command_must_validate_season_year_required()
    {        
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::SEASON_YEAR_OPTION, null);

        $this->artisan($command)
            ->expectsOutput('The season year field must be at least 1.')
            ->assertExitCode(SeasonUpdateSubscriptionsCommand::INVALID);
    }
}