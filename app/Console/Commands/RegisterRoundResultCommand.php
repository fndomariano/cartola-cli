<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register';
 
    protected $description = 'Register Round Result';
 
    public function handle(): void
    {
        $round = $this->ask('Rodada: ');

        (new \App\Services\RoundResultService)->handle($round);
    }
}