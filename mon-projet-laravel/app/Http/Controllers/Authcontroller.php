<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



use Illuminate\Http\Request;

class Authcontroller extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        // Debug: Vérifiez l'utilisateur et le mot de passe
        \Log::info('Tentative de connexion', [
            'email' => $request->email,
            'user_exists' => !!$user,
            'password_match' => $user ? Hash::check($request->password, $user->password) : false
        ]);
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect',
                'errors' => [
                    'email' => ['Ces identifiants ne correspondent pas à nos enregistrements']
                ]
            ], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }
    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during logout'], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'role' => 'required|in:client,prestataire,admin',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'role' => $request->role,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($user->role === 'client') {
            $user->client()->create();
        } elseif ($user->role === 'prestataire') {
            $user->prestataire()->create();
        } elseif ($user->role === 'admin') {
            $user->admin()->create();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
    public function userWithProfile()
    {
        $user = auth()->user();
        
        return response()->json([
            'user' => $user,
            'profile' => $user->profile, // Assurez-vous que cette relation existe
            'profile_type' => $user->role // Utilisez directement le champ role
        ]);
    }
    public function user()
    {
        return response()->json(auth()->user());
    }
}
