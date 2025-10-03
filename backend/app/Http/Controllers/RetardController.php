<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retard;
use Carbon\Carbon;

class RetardController extends Controller
{
    
    public function getUserRetard(Request $request){

        $user = auth()->user();

        $query = Retard::with('employer')->where('id_employer', $user->id_employer);
        $Status = $request->filled('statut');
        $Dates = $request->filled('date_debut') && $request->filled('date_fin');

        if ($Status) {
            $query->where('valider_par_comite', $request->statut);
        }

        if ($Dates) {
            $query->whereBetween('date_retard', [$request->date_retard, $request->date_fin]);
        }

        if (!$Status && !$Dates) {
            $now = now();
            $query->whereMonth('date_retard', $now->month)
                  ->whereYear('date_retard', $now->year);
        }

        $query = $query->orderBy('date_retard', 'desc');
       $retards = $query->paginate(7);

        return response()->json([
            'message' => 'liste des retards',
            'data' =>$retards
        ]);
    }

    public function getRetard(Request $request){ 
        
        $query = Retard::with('employer');

        $Status = $request->filled('statut');
        $Dates = $request->filled('date_debut') && $request->filled('date_fin');

        if ($Status) {
            $query->where('valider_par_comite', $request->statut);
        }

        if ($Dates) {
            $query->whereBetween('date_retard', [$request->date_debut, $request->date_fin]);
        }

        if (!$Status && !$Dates) {
            $now = now();
            $query->whereMonth('date_retard', $now->month)
                  ->whereYear('date_retard', $now->year);
        }

        $query = $query->orderBy('date_retard', 'desc');
        $retards = $query->paginate(7);

        return response()->json([
            'message' => 'liste des retards',
            'data' => $retards
        ]);
    }

    public function addMotifRetard(Request $request, $id)
    {
        $retard = Retard::find($id);

        $request->validate([
            'motif' => 'required'
        ]);

        $retard->motif = $request->motif;
        $retard->save();

        return response()->json([
            'message' => 'Retard Modifier',
            'data' => $retard
        ]);
    }

    public function validerRetard($id) {
       
        $retard = Retard::find($id);

        $retard->valider_par_comite = True;
        $retard->save();

        return response()->json([
            'message' => 'Retard Valide',
            'data' => $retard
        ]);
    }

}
