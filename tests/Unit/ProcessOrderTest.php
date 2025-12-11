<?php

namespace Tests\Unit;

use App\Jobs\ProcessOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessOrderTest extends TestCase
{
    /**
     * A basic unit test to test job being pushed on queue.
     */
    public function test_job_pushed_successfully(): void
    {
        // set up fakes
        Queue::fake([
            ProcessOrder::class
        ]);

        dispatch(new ProcessOrder);

        Queue::assertPushed(ProcessOrder::class, 1);
    }

    public function test_scheduled_job_is_dispatched(): void
    {
        // set up fake
        Bus::fake();

        // set up timing
        $timeToRun = Carbon::now()->addMinutes(5);
        Carbon::setTestNow($timeToRun);

        // run task scheduler
        Artisan::call('schedule:run');

        Bus::assertDispatched(ProcessOrder::class);
        Bus::assertDispatchedTimes(ProcessOrder::class, 1);
    }
}
