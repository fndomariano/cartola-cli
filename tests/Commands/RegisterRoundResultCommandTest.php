<?php 

namespace Tests\Commands;

use Tests\TestCase;

class RegisterRoundResultCommandTest extends TestCase
{ 
    public function test_that_command_must_register_round_result() : void
    {
        $this->artisan('round-result:register')
            ->expectsQuestion('Rodada', 10)
            ->assertExitCode(0);
    }
}