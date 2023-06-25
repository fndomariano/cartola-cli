<?php 

namespace Tests\Commands;

use App\Console\Commands\RemoveRoundResultCommand;
use App\Models\RoundResult;
use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Mockery;
use Tests\TestCase;

class RemoveRoundResultCommandTest extends TestCase
{
    private const COMMAND_NAME = 'round-result:remove';
    private const LEAGUE_OPTION = '--league';
    private const SEASON_YEAR_OPTION = '--seasonYear';
    private const ROUND_OPTION = '--round';

    public function test_that_command_must_remove_round_result() : void
    {
        $round = 10;
        $seasonYear = 2023;
        $leagueSlug = 'cartolas-da-ruindade';
        
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $roundResultService = Mockery::mock(RoundResultService::class);  

        $roundResultService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $roundResultService
            ->shouldReceive('remove')
            ->with($leagueSlug, $seasonYear, $round)
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $command = sprintf(
            "%s %s=%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::LEAGUE_OPTION, $leagueSlug, 
            self::SEASON_YEAR_OPTION, $seasonYear, 
            self::ROUND_OPTION, $round
        );

        $this->artisan($command)
            ->expectsOutput('Round result was removed successfully!')
            ->assertExitCode(RemoveRoundResultCommand::SUCCESS);
    }

    public function test_that_command_must_validate_league_option_required()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, null);
        
        $this->artisan($command)
            ->expectsOutput('The league field is required.')
            ->assertExitCode(RemoveRoundResultCommand::INVALID);
    }
    
    public function test_that_command_must_validate_season_year_option_required()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::SEASON_YEAR_OPTION, null);
        
        $this->artisan($command)
            ->expectsOutput('The season year field must be at least 1.')
            ->assertExitCode(RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_round_option_min_value()
    {
        $command = sprintf(
            "%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::SEASON_YEAR_OPTION, 2012, 
            self::ROUND_OPTION, null
        );
        
        $this->artisan($command)
            ->expectsOutput('The round field must be at least 1.')
            ->assertExitCode(RemoveRoundResultCommand::INVALID);        
    }
}