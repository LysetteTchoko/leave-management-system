<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conge;
use App\Http\Requests\CongeRequest;

class CongeController extends Controller
{
    public function getConge(Request $request)
    {
        $query = Conge::with('employer');

        $Status = $request->filled('statut');
        $Dates = $request->filled('date_debut') && $request->filled('date_fin');

        if ($Status) {
            $query->where('statut', $request->statut);
        }

        if ($Dates) {
            $query->whereBetween('date_debut', [$request->date_debut, $request->date_fin]);
        }

        if (!$Status && !$Dates) {
            $now = now();
            $query->whereMonth('date_debut', $now->month)
                  ->whereYear('date_debut', $now->year);
        }

        $query = $query->orderByRaw("FIELD(statut, 'en_attente') DESC")
                        ->orderBy('date_debut', 'asc');
        $conges = $query->paginate(10);

        return response()->json([
            'message' => 'liste des Conges',
            'data' => $conges
        ]);
    }

    public function getUserConge(Request $request){

        $user = auth()->user();

        $query = Conge::with('employer')->where('id_employer', $user->id_employer);
        $Status = $request->filled('statut');
        $Dates = $request->filled('date_debut') && $request->filled('date_fin');

        if ($Status) {
            $query->where('statut', $request->statut);
        }

        if ($Dates) {
            $query->whereBetween('date_debut', [$request->date_debut, $request->date_fin]);
        }

        if (!$Status && !$Dates) {
            $now = now();
            $query->whereMonth('date_debut', $now->month)
                  ->whereYear('date_debut', $now->year);
        }

        $query = $query->orderByRaw("FIELD(statut, 'en_attente') DESC")
                        ->orderBy('date_debut', 'asc');
        $conges = $query->paginate(7);

        return response()->json([
            'message' => 'liste des Conges',
            'data' => $conges
        ]);
    }

    public function addConge(CongeRequest $request)
    {
        $user = auth()->user();
        $employer = $user->id_employer;
   
        $conge = Conge::create([
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type' => $request->type,
            'statut' =>'en_attente',
            'decision_comite' => 'non_traite',
            'id_employer' => $employer
        ]);

        if($conge){
            return response()->json([
                'message' => 'Conge créé avec succès',
                'data' => $conge
            ]);
        }        
    }

    public function valideConge(Request $request, $id)
    {
        $conge = Conge::find($id);

        $request->validate([
            'decision_comite' => 'required|in:approuve,rejete',
        ]);

        $conge->decision_comite = $request->decision_comite;
        $conge->statut = $request->decision_comite;
        $conge->save();

        return response()->json([
            'message' => 'Conge Valide',
            'data' => $conge
        ]);
    }
}
