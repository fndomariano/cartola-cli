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

    public function test_that_command_must_update_subscriptions() : void
    {
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $seasonService = Mockery::mock(SeasonService::class);  

        $seasonService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $seasonService
            ->shouldReceive('updateSubscriptions')
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(SeasonService::class, $seasonService);

        $this->artisan(self::COMMAND_NAME)
            ->expectsOutput('Season subscriptions have been updated successfully!')
            ->assertExitCode(SeasonUpdateSubscriptionsCommand::SUCCESS);
    }
}