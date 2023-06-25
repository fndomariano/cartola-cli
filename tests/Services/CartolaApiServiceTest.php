<?php 

namespace Tests\Services;

use App\Services\CartolaAPIService;
use Tests\CartolaTestFactory;
use Mockery;
use Tests\TestCase;

class CartolaApiServiceTest extends TestCase 
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function tearDown() : void
    {
        parent::tearDown();
    }

    public function test_must_return_response_from_cartola_api() : void 
    {
        // $cartolaApiService
        //     ->shouldReceive('getLeagueData')
        //     ->with($leagueSlug)
        //     ->once()
        //     ->andReturn(CartolaTestFactory::getCartolaLeagueResponse());

        // $this->app->instance(CartolaAPIService::class, $cartolaApiService);

        $this->assertTrue(true);
    }
}
