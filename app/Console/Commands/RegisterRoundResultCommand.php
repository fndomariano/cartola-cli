<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterRoundResultCommand extends Command
{    
    protected $signature = 'round-result:register';
 
    protected $description = 'Insert teams scores by round';
 
    public function handle(): void
    {
        $round = $this->ask('Rodada: ');

        (new \App\Services\RoundResultService)->handle($round);
    }
}