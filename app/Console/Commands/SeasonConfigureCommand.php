<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeasonConfigureCommand extends Command
{    
    protected $signature = 'season:configure';
 
    protected $description = 'Configure Season';
 
    public function handle(): void
    {
        $year = $this->ask('Ano: ');
        $valueRound = $this->ask('Valor rodada: ');
        $valueSubscription = $this->ask('Valor inscrição: ');
        $numberExemptPlayersRound = $this->ask('Número de jogadores isentos por rodada: ');

        (new \App\Services\SeasonService)->handle($year, $valueRound, $valueSubscription, $numberExemptPlayersRound);
    }
}