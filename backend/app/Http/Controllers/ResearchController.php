<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ResearchRequest;
use App\Models\Presence;
use App\Models\Retard;
use App\Models\Conge;
use Carbon\Carbon;

class ResearchController extends Controller
{
    public function researchPresence(ResearchRequest $request)
    {   
        $startDate = $request->startdate;
        $endDate = $request->endDate;

        $data =Presence::with('employer') 
            ->whereBetween('date_presence', [$startDate, $endDate])
            ->get();

        return response()->json([
            'message' => 'user informations',
            'data' => $data
        ]);
    }

    public function researchRetard(ResearchRequest $request)
    {   
        $startDate = $request->startdate;
        $endDate = $request->endDate;

        $data = Retard::with('employer') 
            ->whereBetween('date_retard', [$startDate, $endDate])
            ->get();

        return response()->json([
            'message' => 'user informations',
            'data' => $data
        ]);
    }

    public function researchConge(ResearchRequest $request)
    {   
        $startDate = $request->startdate;
        $endDate = $request->endDate;

        $data = Conge::with('employer') 
            ->whereBetween('date_debut', [$startDate, $endDate])
            ->get();

        return response()->json([
            'message' => 'user informations',
            'data' => $data
        ]);
    }
    
    public function statUser()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $employer = $user->employer;

        $stats = [
            'retards_mois' =>$employer->retard()
                ->whereMonth('date_retard', $now->month)
                ->whereYear('date_retard', $now->year)
                ->count(),

            'absences_mois' => $employer->presence()
                ->where('statut', 'absent')
                ->whereMonth('date_presence', $now->month)
                ->whereYear('date_presence', $now->year)
                ->count(),
            'presence_mois' => $employer->presence()
                ->whereIn('statut', ['present', 'hors_zone'])
                ->whereMonth('date_presence', $now->month)
                ->whereYear('date_presence', $now->year)
                ->count(),
        ];
        return response()->json([
            'message' => 'User Statistics',
            'data' => $stats
        ]);

    }

     public function statGlobal()
    {
        $today = Carbon::today();

        $stats = [
            'nombre_user' => User::count(),

            'presence_mois' => Presence::whereIn('statut', ['present', 'hors_zone'])
                ->whereMonth('date_presence', $today->month)
                ->whereYear('date_presence', $today->year)
                ->count(),

            'absences_mois' => Presence::where('statut', 'absent')
                ->whereMonth('date_presence', $today->month)
                ->whereYear('date_presence', $today->year)
                ->count(),

            'retards_mois' => Retard::whereMonth('date_retard', $today->month)
                ->whereYear('date_retard', $today->year)
                ->count()
        ];
        
        return response()->json([
            'message' => 'User Statistics',
            'data' => $stats
        ]);
    }
}
