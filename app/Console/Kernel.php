<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendYesterdayActivityReport;

// InfyOm Generator Commands
use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ScaffoldGeneratorCommand::class,
        SendYesterdayActivityReport::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send daily activity report for yesterday at 00:01
        // $schedule->command('report:activity-yesterday')->dailyAt('00:01');
        
        // TESTING: Run every 5 minutes
        $schedule->command('report:activity-yesterday')->everyFiveMinutes();
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
