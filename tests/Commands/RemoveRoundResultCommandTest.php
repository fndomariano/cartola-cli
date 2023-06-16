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
    private const YEAR_SEASON_OPTION = '--yearSeason';
    private const ROUND_OPTION = '--round';

    public function test_that_command_must_remove_round_result() : void
    {
        $round = 10;
        $yearSeason = 2023;
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
            ->with($leagueSlug, $yearSeason, $round)
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $command = sprintf(
            "%s %s=%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::LEAGUE_OPTION, $leagueSlug, 
            self::YEAR_SEASON_OPTION, $yearSeason, 
            self::ROUND_OPTION, $round
        );

        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::SUCCESS);       
    }

    public function test_that_command_must_validate_league_option_when_null()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, null);
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_league_option_when_empty()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, "");
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_year_season_option_when_null()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::YEAR_SEASON_OPTION, null);
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_year_season_option_when_empty()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::YEAR_SEASON_OPTION, "");
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_round_option_when_null()
    {
        $command = sprintf(
            "%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::YEAR_SEASON_OPTION, 2012, 
            self::ROUND_OPTION, null
        );

        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_round_option_when_empty()
    {
        $command = sprintf(
            "%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::YEAR_SEASON_OPTION, 2012, 
            self::ROUND_OPTION, ""
        );
        
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RemoveRoundResultCommand::INVALID);
    }
}