<?php

namespace App\Http\Controllers\Api;

use App;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationController extends Controller
{
    protected $perPage = 10;
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request): ResourceCollection
    {
        $applications = Application::join('customers', 'applications.customer_id', '=', 'customers.id')
            ->join('plans', 'plans.id', '=', 'applications.plan_id');

        if ($request->has('plan_type')) {
            $applications = $applications->where('plans.type', $request->get('plan_type'));
        }
        $collection = $applications->orderBy('id', 'desc')->paginate($this->perPage);

        return ApplicationResource::collection($collection);
    }
}
