<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class B2bService
{
    public function submitApplication(Application $application): void
    {
        try {
            $endpoint = env('NBN_B2B_ENDPOINT');
            $response = Http::post($endpoint, [
                'address_1' => $application->address_1,
                'address_2' => $application->address_2,
                'city' => $application->city,
                'state' => $application->state,
                'postcode' => $application->postcode,
                'plan_name' => $application->plan_name,
            ]);
            $data = $response->json();
            if (isset($data['status'])) {
                if ($data['status'] == 'Successful') {
                    $application->status = ApplicationStatus::Complete;
                    $application->order_id = $data['id'];
                } else {
                    $application->status = ApplicationStatus::OrderFailed;
                }
                $application->save();
            }

            Log::info('B2B order process success.');
        } catch (Exception $e) {
            Log::error('B2B Sending failed. Error: ' . $e->getMessage());
        }
    }
}
