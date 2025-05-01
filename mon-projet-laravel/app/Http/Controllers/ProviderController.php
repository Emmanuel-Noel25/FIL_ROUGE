<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = Provider::query()->with('user', 'services', 'availabilities');
        
        // Filtrer par service
        if ($request->has('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->service_id);
            });
        }
        
        // Filtrer par jour de disponibilité
        if ($request->has('day_of_week')) {
            $query->whereHas('availabilities', function ($q) use ($request) {
                $q->where('day_of_week', $request->day_of_week);
            });
        }
        
        $providers = $query->paginate(10);
        return ProviderResource::collection($providers);
    }
    
    public function show(Provider $provider)
    {
        $provider->load('user', 'services', 'availabilities');
        return new ProviderResource($provider);
    }
    
    public function updateServices(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $data = $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.price' => 'required|numeric|min:0',
        ]);
        
        $serviceData = [];
        foreach ($data['services'] as $service) {
            $serviceData[$service['id']] = ['price' => $service['price']];
        }
        
        $user->provider->services()->sync($serviceData);
        
        return response()->json([
            'message' => 'Services mis à jour avec succès',
            'services' => $user->provider->services()->with('pivot')->get()
        ]);
    }
}