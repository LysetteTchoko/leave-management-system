<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Retard;
use carbon\Carbon;

class PresenceController extends Controller
{
    public function getUserPresence(Request $request)
    {
        $user = auth()->user();
        $employer = $user->employer;
        $now = Carbon::now();

        $query = Presence::where('id_employer', $employer->id_employer)
            ->whereMonth('date_presence', $now->month)
            ->whereYear('date_presence', $now->year);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_presence', [$request->date_debut, $request->date_fin]);
        }

        $presences = $query->orderBy('date_presence', 'desc')->paginate(5);

        $retards = Retard::where('id_employer', $employer->id_employer)
            ->whereMonth('date_retard', $now->month)
            ->whereYear('date_retard', $now->year)
            ->get();

     // Fusion retard et presence
        $presenceRetard = $presences->getCollection()->map(function ($presence) use ($retards) {
            $duree_retard = null;

            foreach ($retards as $retard) {
                if ($retard->date_retard == $presence->date_presence) {
                    $duree_retard = $retard->duree_retard;
                    break;
                }
            }

                if ($presence->statut === 'present' && $duree_retard) {
                    $statut_final = 'retard';
                } else {
                    $statut_final = $presence->statut;
                    $duree_retard = null; 
                }

            return [
                'date' => $presence->date_presence,
                'statut' => $statut_final,
                'heure_arrivee' => $presence->heure_arrivee,
                'heure_depart' => $presence->heure_depart,
                'duree_retard' => $duree_retard,
            ];
        });

        // Remettre la collection 
        $presences->setCollection($presenceRetard);

        return response()->json([
            'message' => 'Liste Presences du user',
            'data' => $presences
        ]);
    }
    public function getAllUserPresence(Request $request)
    {
        $now = Carbon::now();

        $query = Presence::with('employer')
            ->whereMonth('date_presence', $now->month)
            ->whereYear('date_presence', $now->year);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_presence', [$request->date_debut, $request->date_fin]);
        }

        if ($request->filled('nom')) {
            $query->whereHas('employer', function($q) use ($request) {
                $q->where('nom', 'like', "%{$request->nom}%");
            });
        }

        $presences = $query->orderBy('date_presence', 'desc')->paginate(5);

        $retards = Retard::whereMonth('date_retard', $now->month)
            ->whereYear('date_retard', $now->year)
            ->get();
        
        $presenceRetard = $presences->getCollection()->map(function ($presence) use ($retards) {
            $duree_retard = null;
            
            foreach ($retards as $retard) {
    
                if ((int)$retard->id_employer == (int)$presence->id_employer && $retard->date_retard == $presence->date_presence) {
                    $duree_retard = $retard->duree_retard;
                    break;
                }
            }

                if ($presence->statut === 'present' && $duree_retard) {
                    $statut_final = 'retard';
                } else {
                    $statut_final = $presence->statut;
                }

            return [
                'matricule' => $presence->employer->matricule,
                'nom' => $presence->employer->nom,
                'prenom' => $presence->employer->prenom,
                'poste' => $presence->employer->poste,
                'date' => $presence->date_presence,
                'statut' => $statut_final,
                'heure_arrivee' => $presence->heure_arrivee,
                'heure_depart' => $presence->heure_depart,
                'duree_retard' => $duree_retard,
            ];
        });

        $presences->setCollection($presenceRetard);

        return response()->json([
            'message' => 'Liste des presences',
            'data' => $presences
        ]);
    }

}
