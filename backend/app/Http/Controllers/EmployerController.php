<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employer;

class EmployerController extends Controller
{
    public function createEmploye(Request $request){

        $employer = Employer::create([
            'matricule' => $request->matricule,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email'=> $request->email,
            'poste' => $request->poste,
            'telephone' => $request->telephone,
            'departement' => $request->departement,
            'date_embauche' => $request->date_embauche,
            'statut' => $request->status
        ]);

        return response()->json([
            'message' => 'Compte créé avec succès',
            'data'    => $employer
        ]);
    }
    
    public function viewEmployer(){
        $employer = Employer::paginate(7);
        return response()->json([
            'message' => 'Liste des employer',
            'data' => $employer
        ]);
    }

    public function deleteEmploye($id)
    {
        $employer = Employer::find($id);
        $employer->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function updateEmploye(Request $request,$id)
    {
        $employer = Employer::find($id);

        if(!empty($request->matricule)){
            $employer->matricule = $request->matricule;
        }
        if(!empty($request->nom)){  
            $employer->nom = $request->nom;
        }
        if(!empty($request->prenom)){
            $employer->prenom = $request->prenom;
        }
        if(!empty($request->email)){
            $employer->email = $request->email;
        }
        if(!empty($request->poste)){
            $employer->poste = $request->poste;
        }
        if(!empty($request->telephone)){
            $employer->telephone = $request->telephone;
        }
        if(!empty($request->departement)){
            $employer->departement = $request->departement;
        }
        if(!empty($request->date_embauche)){
            $employer->date_embauche = $request->date_embauche;
        }
        if(!empty($request->status)){
            $employer->status = $request->status;
        }

        $employer->save();
        return response()->json(['message' => 'Utilisateur modifier avec succès']);
    }
}
