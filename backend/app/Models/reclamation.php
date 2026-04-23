<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reclamation extends Model
{
     use HasFactory;
    protected $fillable = [
        'facture_id',
        'statut',
        'reponse',

    ];

    public function facture()
    {
        return $this->belongsTo(facture::class);
    }
}
