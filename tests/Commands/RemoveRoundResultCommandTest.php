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

    public function test_that_command_must_remove_round_result() : void
    {
        $round = 10;
        
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $roundResultService = Mockery::mock(RoundResultService::class);  

        $roundResultService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $roundResultService
            ->shouldReceive('remove')
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $this->artisan(self::COMMAND_NAME)
            ->expectsOutput('Round results were removed successfully!')
            ->assertExitCode(RemoveRoundResultCommand::SUCCESS);
    }

    public function test_that_command_must_throw_exception() : void
    {
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $roundResultService = Mockery::mock(RoundResultService::class);  

        $roundResultService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $roundResultService
            ->shouldReceive('remove')
            ->once()
            ->andThrow(\Exception::class);
        
            
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $roundResultService);
            
        $this->artisan(self::COMMAND_NAME)
            ->assertExitCode(RemoveRoundResultCommand::INVALID);
    }
      
}