<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceControllers extends Controller
{
    public function index()
    {
        $services = Service::all();
        return ServiceResource::collection($services);
    }
    
    public function show(Service $service)
    {
        return new ServiceResource($service);
    }
}