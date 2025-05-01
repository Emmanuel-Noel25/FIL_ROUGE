<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isClient()) {
            $requests = $user->client->requests()->with('provider.user', 'service')->get();
        } elseif ($user->isProvider()) {
            $requests = $user->provider->requests()->with('client.user', 'service')->get();
        } else {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        return RequestResource::collection($requests);
    }
    
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->isClient()) {
            return response()->json(['message' => 'Seuls les clients peuvent créer des demandes'], 403);
        }
        
        $data = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'service_id' => 'required|exists:services,id',
            'date_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:30', // durée minimale de 30 minutes
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Vérifier si le prestataire offre ce service
        $provider = \App\Models\Provider::findOrFail($data['provider_id']);
        $serviceExists = $provider->services()->where('services.id', $data['service_id'])->exists();
        
        if (!$serviceExists) {
            return response()->json([
                'message' => 'Ce prestataire n\'offre pas ce service'
            ], 400);
        }
        
        // Créer la demande
        $data['client_id'] = $user->client->id;
        $data['status'] = 'pending';
        
        $serviceRequest = ServiceRequest::create($data);
        
        return new RequestResource($serviceRequest);
    }
    
    public function show(ServiceRequest $request)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est autorisé à voir cette demande
        if (($user->isClient() && $request->client_id === $user->client->id) || 
            ($user->isProvider() && $request->provider_id === $user->provider->id)) {
            $request->load(['client.user', 'provider.user', 'service']);
            return new RequestResource($request);
        }
        
        return response()->json(['message' => 'Non autorisé'], 403);
    }
    
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $user = $request->user();
        
        // Seul le prestataire peut mettre à jour le statut
        if (!$user->isProvider() || $serviceRequest->provider_id !== $user->provider->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $data = $request->validate([
            'status' => 'required|in:accepted,completed,cancelled',
        ]);
        
        $serviceRequest->update($data);
        
        return new RequestResource($serviceRequest);
    }
}