<?php 

namespace Tests\Commands;

use App\Console\Commands\RegisterRoundResultCommand;
use App\Models\RoundResult;
use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Mockery;
use Tests\TestCase;

class RegisterRoundResultCommandTest extends TestCase
{     
    private const COMMAND_NAME = 'round-result:register';
    private const LEAGUE_OPTION = '--league';
    private const ROUND_OPTION = '--round';

    public function setUp() : void
    {
        parent::setUp();
    }

    public function tearDown() : void
    {
        parent::tearDown();
    }

    public function test_that_command_must_register_round_result() : void
    {
        $round = 10;
        $leagueSlug = 'cartolas-da-ruindade';
        
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $roundResultService = Mockery::mock(RoundResultService::class);  

        $roundResultService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $roundResultService
            ->shouldReceive('register')
            ->with($round, $leagueSlug)
            ->once()
            ->andReturnNull();            
        
            
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $command = sprintf(
            "%s %s=%s %s=%s", 
            self::COMMAND_NAME, 
            self::LEAGUE_OPTION, $leagueSlug, 
            self::ROUND_OPTION, $round
        );

        $result = $this->artisan($command);
        
        $this->assertTrue($result == RegisterRoundResultCommand::SUCCESS);       
    }

    public function test_that_command_must_validate_league_option_when_null()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, null);
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RegisterRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_league_option_when_empty()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::LEAGUE_OPTION, "");
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RegisterRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_round_option_when_null()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::ROUND_OPTION, null);
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RegisterRoundResultCommand::INVALID);
    }

    public function test_that_command_must_validate_round_option_when_empty()
    {
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, self::ROUND_OPTION, "");
        $result = $this->artisan($command);
        
        $this->assertTrue($result == RegisterRoundResultCommand::INVALID);
    }
    
}