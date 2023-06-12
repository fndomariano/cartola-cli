<?php 

namespace Tests\Commands;

use App\Console\Commands\RegisterRoundResultCommand;
use App\Models\RoundResult;
use App\Services\RoundResultService;
use App\Services\CartolaAPIService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RegisterRoundResultCommandTest extends TestCase
{ 
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
        
        $serviceMock = Mockery::mock(RoundResultService::class);  

        $serviceMock
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $serviceMock
            ->shouldReceive('register')
            ->with($round, $leagueSlug)
            ->once()
            ->andReturnNull();            
        
            
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(RoundResultService::class, $serviceMock);
            
        $this->artisan('round-result:register');
            
    }
}