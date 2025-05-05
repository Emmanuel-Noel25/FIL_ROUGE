<?php

namespace App\Http\Controllers;

use App\Models\Prestataire;
use Illuminate\Http\Request;

class PrestataireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prestataire $prestataire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prestataire $prestataire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prestataire $prestataire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prestataire $prestataire)
    {
        //
    }
    public function search(Request $request)
    {
        $keyword = $request->query('q');

        $query = Prestataire::with(['user', 'services'])
            ->when($keyword, function ($q) use ($keyword) { // Closure use
                $q->whereHas('user', function ($q2) use ($keyword) {
                    $q2->where('name', 'like', "%{$keyword}%");
                })
                ->orWhereHas('services', function ($q2) use ($keyword) {
                    $q2->where('title', 'like', "%{$keyword}%"); // 'title' au lieu de 'name'
                });
            });

        $prestataires = $query->get(); // Nom de variable corrigé

        if ($prestataires->isEmpty()) {
            return response()->json(['message' => 'Aucun prestataire trouvé.'], 404);
        }
        return response()->json($prestataires, 200);
    }
}