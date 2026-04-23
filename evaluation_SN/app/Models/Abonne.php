<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonne extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nom',
        'prenom',
        'ville',
        'quartier',
        'numeroCompteur',
        'typeAbonnement',
        'dateCreation',
    ];
    
    public function factures()
    {
        return $this->hasMany(Facture::class, 'abonne_id');
    }
}
