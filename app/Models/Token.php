<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    public $timestamps = false;

    protected $table = "token";

    protected $fillable = [
        'valeur',
        'date_heure_creation',
        'date_heure_expiration',
        'id_utilisateur',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
