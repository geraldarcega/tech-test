<?php

namespace Tests\Unit;

use App\Jobs\ProcessOrder;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test to test job being pushed on queue.
     */
    public function test_job_pushed_successfully(): void
    {
        // set up fakes
        Queue::fake([
            ProcessOrder::class
        ]);
        $application = Application::factory()->create();
        dispatch(new ProcessOrder($application));

        Queue::assertPushed(ProcessOrder::class, 1);
    }

    /**
     * A basic unit test to dispatch job using command.
     */
    public function test_run_job_through_command(): void
    {
        // set up fake
        Bus::fake();

        // set up data
        $numOfApplications = 5;
        Application::factory($numOfApplications)
            ->nbnPlan()
            ->create(['status' => 'order']);

        // run task scheduler
        Artisan::call('app:process-order');

        Bus::assertDispatched(ProcessOrder::class);
        Bus::assertDispatchedTimes(ProcessOrder::class, $numOfApplications);
    }
}
