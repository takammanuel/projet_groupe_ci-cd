<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;
    protected $fillable = [
        'abonne_id',
        'consommation',
        'montantTotal',
        'dateEmission',
        'statut',
    ];

    public function abonne()
    {
        return $this->belongsTo(Abonne::class);
    }
}
