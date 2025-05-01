<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $availabilities = $user->provider->availabilities;
        return AvailabilityResource::collection($availabilities);
    }
    
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $data = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        
        $availability = $user->provider->availabilities()->create($data);
        
        return new AvailabilityResource($availability);
    }
    
    public function update(Request $request, Availability $availability)
    {
        $user = $request->user();
        if (!$user->isProvider() || $availability->provider_id !== $user->provider->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $data = $request->validate([
            'day_of_week' => 'sometimes|integer|between:0,6',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
        ]);
        
        $availability->update($data);
        
        return new AvailabilityResource($availability);
    }
    
    public function destroy(Request $request, Availability $availability)
    {
        $user = $request->user();
        if (!$user->isProvider() || $availability->provider_id !== $user->provider->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $availability->delete();
        
        return response()->json(['message' => 'Disponibilité supprimée']);
    }
}