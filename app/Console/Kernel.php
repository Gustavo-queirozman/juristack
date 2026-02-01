<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Executar verificação de atualizações a cada 6 horas
        $schedule->command('datajud:monitor-updates')
                 ->everyHours(6)
                 ->withoutOverlapping()
                 ->onOneServer();

        // Opcional: Executar também a cada hora para testes
        // $schedule->command('datajud:monitor-updates --limit=10')
        //          ->hourly()
        //          ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
