<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employer;

class Retard extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_retard';

    protected $fillable = [
        'id_employer',
        'date_retard',
        'heure_arrivee',
        'heure_normale',
        'duree_retard',
        'motif',
        'valider_par_comite',
    ];

    public function employer() {
       return $this->belongsTo(Employer::class,'id_employer','id_employer');
    }
}
