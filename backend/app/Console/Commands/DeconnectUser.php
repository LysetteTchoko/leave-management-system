<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presence;
use App\Models\Conge;
use App\Models\User;
use Carbon\Carbon;

class DeconnectUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deconnect-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deconnecter les utilisateurs restants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('H:i:s');
        $today = Carbon::today();

        $users = User::with('employer')->get();

        foreach ($users as $user) {
            $id_employer = $user->id_employer;

            // Verifier s'il y a deja une presence aujourd'hui
            $presence = Presence::where('id_employer', $id_employer)
                ->whereDate('date_presence', $today)
                ->first();

            // Verifier les conge du jour
            $conge = Conge::where('id_employer', $id_employer)
                ->whereDate('date_debut', '<=', $today)
                ->whereDate('date_fin', '>=', $today)
                ->first();

            // Si conge approuve, on met le statut conge dans la table presence
            if ($conge && $conge->statut == 'approuve') {
                if ($presence) {
                    $presence->statut = 'conge';
                    $presence->save();
                } else {
                    Presence::create([
                        'id_employer' => $id_employer,
                        'date_presence' => $today,
                        'heure_arrivee' => null,
                        'heure_depart' => null,
                        'statut' => 'conge'
                    ]);
                }
            }

            // si presence et heure_depart null
            // sinon si pas de présence, on cree la presence et met un status absent
            if ($presence) {
                if (!$presence->heure_depart) {
                    $presence->heure_depart = $now;
                    $presence->save();
                }
            } else {
                Presence::create([
                    'id_employer' => $id_employer,
                    'date_presence' => $today,
                    'heure_arrivee' => null,
                    'heure_depart' => null,
                    'statut' => 'absent'
                ]);
            }
        }

    }
}
 