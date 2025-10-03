<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_conge';

    protected $fillable = [
        'date_debut',
        'date_fin',
        'type',
        'statut',
        'decision_comite',
        'id_employer'
    ];

    public function employer() {
      return $this->belongsTo(Employer::class,'id_employer','id_employer');
    }
}
