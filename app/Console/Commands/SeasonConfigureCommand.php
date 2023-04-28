<?php 

namespace App\Console\Commands;

use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class SeasonConfigureCommand extends Command
{    
    protected $signature = 'season:configure';
 
    protected $description = 'Configure Season';
 
    public function handle(): void
    {
        $this->info('Hello, Motherfucker');


        (new \App\Services\SeasonService)->configure();
    }
}