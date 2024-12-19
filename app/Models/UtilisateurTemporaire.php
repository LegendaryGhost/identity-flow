<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilisateurTemporaire extends Model
{
    protected $table = 'utilisateur_temporaire';

    protected $fillable = [
        'email', 'nom', 'prenom', 'mot_de_passe', 'date_naissance', 'token_verification'
    ];

    protected $hidden = ['mot_de_passe', 'token_verification', 'date_heure_creation'];
}
