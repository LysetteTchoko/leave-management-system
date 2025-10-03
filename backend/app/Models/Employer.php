<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employer extends Model
{
    use HasFactory;

   protected $primaryKey = 'id_employer';

   protected $fillable = [
      'matricule',
      'nom',
      'prenom',
      'email',
      'poste',
      'telephone',
      'departement',
      'date_embauche',
      'status'
   ];


    public function user() {
       return $this->hasOne(User::class,'id_employer');
    }

    public function retard() {
       return $this->hasMany(Retard::class,'id_employer');
    }

    public function presence() {
       return $this->hasMany(Presence::class,'id_employer');
    }

    public function conge() {
       return $this->hasMany(Conge::class,'id_employer');
    }
}
