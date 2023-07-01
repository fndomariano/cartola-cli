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
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $roundResultService = Mockery::mock(RoundResultService::class);  

        $roundResultService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $roundResultService
            ->shouldReceive('register')
            ->once()
            ->andReturnNull();
        
            
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $this->artisan(self::COMMAND_NAME)
            ->expectsOutput('Round results were registered successfully!')
            ->assertExitCode(RegisterRoundResultCommand::SUCCESS);
    }    
}