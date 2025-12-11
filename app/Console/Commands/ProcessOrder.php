<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use App\Jobs\ProcessOrder as ProcessOrderJob;

class ProcessOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicationOrders = Application::orderReady();
        if ($applicationOrders->count()) {
            foreach ($applicationOrders->get() as $application) {
                dispatch(new ProcessOrderJob($application));
            }
        }
    }
}
