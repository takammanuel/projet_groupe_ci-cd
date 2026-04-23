<?php

namespace App\Http\Controllers;

use App\Models\reclamation;
use App\Models\Facture;
use Illuminate\Http\Request;

class reclamationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reclamation=reclamation::with("facture")->get();

        return response()->json($reclamation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $donneesValidee=$request->validate([
           'facture_id' => 'required|exists:abonnes,id',
                'reponse' => 'required|string|max:255',
                'statut' => 'required|in:Emise,Payee',

        ]);
         $reclamation= reclamation::create($donneesValidee);
         return response()->json($reclamation,201);

    }

    /**
     * Display the specified resource.
     */
    public function show(reclamation $reclamation)
    {
        return response()->json($reclamation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, reclamation $reclamation)
    {
        $donneesValidee=$request->validate([
              'facture_id' => 'required|exists:abonnes,id',
                'reponse' => 'required|string|max:255',
                'statut' => 'required|in:Emise,Payee',

        ]);
        $reclamation->update($donneesValidee);
        return response()->json($reclamation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(reclamation $reclamation)
    {
        $reclamation->delete();
        return response()->json($reclamation);
    }
}
