<?php

namespace App\Jobs;

use App\Models\Application;
use App\Services\B2bService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Application $application
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $service = new B2bService();
            $service->submitApplication($this->application);

            Log::info("Application order successfully process.");
        } catch (\Exception $e) {
            Log::info("Application order process failed. " . $e->getMessage());
        }
    }
}
