<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_presence';

    protected $fillable = [
        'id_employer' ,
        'date_presence',
        'heure_arrivee',
        'statut',
        'heure_depart'
    ];

    public function employer() {
       return $this->belongsTo(Employer::class,'id_employer','id_employer');
    }
}
