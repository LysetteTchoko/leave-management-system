<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employer;
use App\Models\Presence;
use App\Models\Retard;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{   
    public function viewUser(){
        
        $user = User::with('employer')->paginate(8);

        return response()->json([
            'message' => 'liste des utilisateur',
            'data' => $user
        ]);
    }

    public function getUserId($id)
    {
        $user = User::with('employer')->findOrFail($id);
        return response()->json(['data' => $user]);
    }

    public function register(RegisterUserRequest $request)
    {
        $employe = Employer::where('email', $request->email)->first();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $folder = env('USER_PHOTO_PATH', 'userProfile');
            $filename = now()->format('Ymd_His') . '_' . mt_rand(1000,9999) . '.' . $extension;
            // stocke dans storage/app/public/userProfile/
            $file->storeAs($folder, $filename, 'public');
            $photoPath = $filename;        
        }
    
        $user = User::create([
            'username'  => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'employe',
            'id_employer' => $employe->id_employer,
            'photo' => $photoPath,
        ]);

        if($user){
            return response()->json([
                'message' => 'Compte créé avec succès',
                'data' => $user
            ]);
        }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé']);
        }
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function updateUser(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'username' => 'sometimes|min:2',
            'email' => 'sometimes|email|unique:users,email,'.$user->id_user,
        ]);

       if (!empty($validated)) {
            $user->update($validated);
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ]);
    }

    public function updateUsers(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'username' => 'sometimes|min:2',
            'email' => 'sometimes|email|unique:users,email,'.$user->id_user.',id_user',
            'role'=> 'sometimes|in:admin,comite,employe',
            'password' => 'sometimes|min:6',
        ]);

        if(isset($validated['username'])){
            $user->username = $validated['username'];
        }
        if(isset($validated['email'])){
            $user->email = $validated['email'];
        }
        if(isset($validated['role'])){
            $user->role = $validated['role'];
        }
        /*if(!empty($validated['password'])){
            $user->password = bcrypt($validated['password']);
        }*/

        $user->save();
        return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user]);
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,comite,employe'
        ]);

        $user = User::find($id);
        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'data' => $user
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $user = auth()->user();
        
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename  = now()->format('Ymd_His') . '_' . mt_rand(1000,9999) . '.' . $extension;
                $folder = env('USER_PHOTO_PATH', 'userProfile');

                //supprimer l'ancienne photo si ça existe
                if($user->photo && Storage::disk('public')->exists('userProfile/'.$user->photo)){
                    Storage::disk('public')->delete('userProfile/'.$user->photo);
                }
                
                // Stocker le fichier
                $file->storeAs($folder, $filename, 'public');
                $user->photo = $filename;
                $user->save();

                return response()->json([
                    'message' => 'Photo mis à jour avec succès',
                    'data' => $user
                ]);
            }else{
                return response()->json([
                    'message' => 'Aucune photo reçue',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $id_employer = $user->id_employer;

        $now = Carbon::now()->format('H:i:s');
        $today = Carbon::today();

        $presence = Presence::where('id_employer', $id_employer)
            ->whereDate('date_presence', $today)
            ->first();
        if ($presence && !$presence->heure_depart)
        {
            $presence->heure_depart = $now;
            $presence->save();
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function getEnumValues(string $table, string $column): array
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $result = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column]);
        $type = $result[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches); //recuperer les elements entre ()

        return array_map(function ($value) {
            return trim($value, "'"); // supprime les apostrophes
        }, explode(',', $matches[1]));
    }

    public function getRoles()
    {
        $roles = $this->getEnumValues('users', 'role');

        return response()->json([
            'roles' => $roles
        ]);
    }

    public function getType() 
    {
        $type = $this->getEnumValues('conges', 'type');

        return response()->json([
            'Typeconges' => $type
        ]);
    }

    public function getStatutPresence() 
    {
        $type = $this->getEnumValues('presences', 'statut');

        return response()->json([
            'StatutPresence' => $type
        ]);
    }

    public function getUser()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $employer = $user->employer; 

        $retard = $employer->retard()
        ->whereDate('date_retard', $today)
        ->first();

         $present = $employer->presence()
        ->whereDate('date_presence', $today)
        ->first();

        $employe = Employer::find($user->id_employer);    
        
        if ($retard)
        {
            $statutToday = 'En retard';
        }elseif($present)
        {
            $statutToday =$present->statut;
        }

        return response()->json([
            'message' => 'utilisateur',
            'data' => $user,
            'statut' => $statutToday,
            'employer' => $employe
        ]);
    }

}
