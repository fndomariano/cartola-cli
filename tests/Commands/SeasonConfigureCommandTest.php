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

    private const ROUND_VALUE_OPTION = '--roundValue';
    private const SUBSCRIPTION_FEE_OPTION = '--subscriptionFee';
    private const NUMBER_EXEMPT_PLAYERS_OPTION = '--numberExemptPlayersRound';

    private float $valueRound = 3;
    private float $subscriptionFee = 30;
    private int $numberExemptPlayersRound = 3;
    
    public function test_that_command_must_configure_season() : void
    {
        $cartolaApiService = Mockery::mock(CartolaAPIService::class)->makePartial();
        
        $seasonService = Mockery::mock(SeasonService::class);  

        $seasonService
            ->shouldReceive('setCartolaApiService')
            ->with($cartolaApiService)
            ->once()
            ->andReturnSelf();
        
        $seasonService
            ->shouldReceive('configure')
            ->with($this->valueRound, $this->subscriptionFee, $this->numberExemptPlayersRound)
            ->once()
            ->andReturnNull();            
        
        $this->app->instance(CartolaAPIService::class, $cartolaApiService);
        $this->app->instance(SeasonService::class, $seasonService);

        $this->artisan(self::COMMAND_NAME)
            ->expectsOutput('Season was configured successfully!')
            ->assertExitCode(SeasonConfigureCommand::SUCCESS);
    }

    public function test_that_command_must_validate_round_value_required()
    {        
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, 
            self::ROUND_VALUE_OPTION, null
        );

        $this->artisan($command)
            ->expectsOutput('The value round field must be at least 1.')
            ->assertExitCode(SeasonConfigureCommand::INVALID);
    }

    public function test_that_command_must_validate_subscription_fee_required()
    {        
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, 
            self::SUBSCRIPTION_FEE_OPTION, null
        );

        $this->artisan($command)
            ->expectsOutput('The subscription fee field must be at least 1.')
            ->assertExitCode(SeasonConfigureCommand::INVALID);
    }

    public function test_that_command_must_validate_number_exempt_players_required()
    {        
        $command = sprintf("%s %s=%s", self::COMMAND_NAME, 
            self::NUMBER_EXEMPT_PLAYERS_OPTION, null
        );

        $this->artisan($command)
            ->expectsOutput('The number exempt players round field must be at least 1.')
            ->assertExitCode(SeasonConfigureCommand::INVALID);
    }

}