<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conge;
use App\Models\Presence;
use App\Models\Retard;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{ 
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; 
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; 
    }

    public function login(AuthRequest $request)
    {
        try{

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response = [];
            $response["token"] = $user->createToken("MyApp")->plainTextToken;
            $employer = $user->employer;

            $entrepriseLat = env('ENTREPRISE_LAT');
            $entrepriseLong = env('ENTREPRISE_LONG');
            $rayonAutorise = env('ENTREPRISE_RAYON');
            $heureDefinie  = env('HEURE_DEBUT');

            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $entrepriseLat,
                $entrepriseLong
            );
            
            $today = now()->toDateString();
            $presenceExists = Presence::where('id_employer', $employer->id_employer)
                ->where('date_presence', $today)
                ->exists();
            
            if ($distance <= $rayonAutorise) {
                if (now()->toTimeString() <= $heureDefinie) {

                    if (!$presenceExists) {
                        Presence::create([
                            'id_employer' => $employer->id_employer,
                            'date_presence' => $today,
                            'heure_arrivee' => now()->toTimeString(),
                            'statut' => 'present',
                        ]);
                    }
                } else {
                    $diff = now()->diffInMinutes(Carbon::parse($heureDefinie));
                    $hours = floor($diff / 60);
                    $minutes = $diff % 60;
                    $diff = sprintf('%02d:%02d:00', $hours, $minutes);

                    $retardExists = Retard::where('id_employer', $employer->id_employer)
                        ->where('date_retard', $today)
                        ->exists();

                    if (!$retardExists && !$presenceExists) {
                        Retard::create([
                            'id_employer' => $employer->id_employer,
                            'date_retard' => $today,
                            'heure_arrivee' => now()->toTimeString(),
                            'heure_normale'=> $heureDefinie,
                            'duree_retard' => $diff,
                            'motif' => 'Non précisé',
                            'valider_par_comite' => false,
                        ]);
                    }
                    if (!$presenceExists) {
                        Presence::create([
                            'id_employer' => $employer->id_employer,
                            'date_presence' => $today,
                            'heure_arrivee' => now()->toTimeString(),
                            'statut' => 'present',
                        ]);
                    }
                }
            } else {

                 if (!$presenceExists) {
                        Presence::create([
                            'id_employer' => $employer->id_employer,
                            'date_presence' => $today,
                            'heure_arrivee' => now()->toTimeString(),
                            'statut' => 'hors_zone',
                        ]);
                    }
                return response()->json([
                    'error' => 'Employé hors zone',
                    'success' => true,
                    'data' => $user,
                    'token' => $response["token"]
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => $user,
                'token' => $response["token"]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Identifiant Incorrect'
            ], 401);
    }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ]);
    }
}

}
