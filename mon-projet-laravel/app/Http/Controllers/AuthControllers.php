<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthControllers extends Controller
{
    public function me(Request $request)
    {
        try {
            $user = $request->user();
    
            if (!$user) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
    
            return response()->json([
                'user' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();
        $userData['password'] = Hash::make($userData['password']);
        
        $user = User::create($userData);
        
        // Créer le profil client ou prestataire
        if ($user->isClient()) {
            Client::create(['user_id' => $user->id]);
        } elseif ($user->isProvider()) {
            Provider::create(['user_id' => $user->id]);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }
    
    // public function login(LoginRequest $request)
    // {
    //     $user = User::where('email', $request->email)->first();
        
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => 'Identifiants invalides'
    //         ], 401);
    //     }
        
    //     $token = $user->createToken('auth_token')->plainTextToken;
        
    //     return response()->json([
    //         'user' => new UserResource($user),
    //         'token' => $token
    //     ]);
    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'token' => $token,
                'user' => $request->user()
            ]);
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
    
    // public function me(Request $request)
    // {
    //     return new UserResource($request->user());
    // }
    
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $userData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
        ]);
        
        $user->update($userData);
        
        // Mise à jour du profil spécifique (client ou prestataire)
        if ($user->isClient() && $request->has('client')) {
            $clientData = $request->validate([
                // 'client.birth_date' => 'sometimes|date',
                // 'client.emergency_contact' => 'sometimes|string|max:255',
                // 'client.medical_info' => 'sometimes|string|max:1000',
            ]);
            
            $user->client->update($clientData['client']);
        } elseif ($user->isProvider() && $request->has('provider')) {
            $providerData = $request->validate([
                'provider.description' => 'sometimes|string|max:1000',
                'provider.experience' => 'sometimes|string|max:255',
                'provider.qualifications' => 'sometimes|string|max:1000',
                'provider.rate_per_hour' => 'sometimes|numeric|min:0',
            ]);
            
            $user->provider->update($providerData['provider']);
        }
        
        return new UserResource($user->fresh());
    }

public function getUserWithProfile(Request $request)
    {
        $user = $request->user();
        $profile = null;
        $profileType = null;

        switch ($user->role) {
            case 'eleve':
                $profile = Eleve::where('user_id', $user->id)->first();
                $profileType = 'client';
                break;
            case 'repetiteur':
                $profile = Repetiteur::where('user_id', $user->id)->first();
                $profileType = 'prestataire';
                break;
            // case 'admin':
            //     $profile = Admin::where('user_id', $user->id)->first();
            //     $profileType = 'admin';
            //     break;
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile,
            'profile_type' => $profileType
        ]);
    }
}