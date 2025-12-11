<?php

namespace Tests\Unit;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\B2bService;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class B2bServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test for successful order processing.
     */
    #[DataProvider('stubProvider')]
    public function test_successful_order_processing(
        string $filename,
        ApplicationStatus $expectedStatus
    ): void
    {
        // setup test data
        $application = Application::factory()
            ->nbnPlan()
            ->create(['status' => 'order']);

        // setup stub file
        $stubPath = base_path("tests/Stubs/{$filename}.json");
        $stubContent = file_get_contents($stubPath);
        Http::fake([
            env('NBN_B2B_ENDPOINT') => Http::response($stubContent, 201, ['Content-Type' => 'application/json']),
        ]);

        // run service
        $service = new B2bService();
        $service->submitApplication($application);

        // get fresh copy of application
        $application->fresh();

        $this->assertSame($expectedStatus, $application->status);
    }

    public static function stubProvider(): Generator
    {
        yield 'Successful' => [ 'nbn-successful-response', ApplicationStatus::Complete ];

        yield 'Failed' => [ 'nbn-fail-response', ApplicationStatus::OrderFailed ];
    }
}
