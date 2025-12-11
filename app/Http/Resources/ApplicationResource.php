<?php

namespace App\Http\Resources;

use App\Enums\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCompleted = $this->status == ApplicationStatus::Complete;
        return [
            'id' => $this->id,
            'customer_name' => $this->first_name . ' ' . $this->last_name,
            'address' => [
                'address1' => $this->address_1,
                'address2' => $this->address_2,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postcode,
            ],
            'plan_type' => $this->type,
            'state' => $this->state,
            'plan_monthly_cost' => number_format($this->monthly_cost, 2),
            'order_id' => $this->when($isCompleted, $this->order_id),
        ];
    }
}
