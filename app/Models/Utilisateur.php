<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Utilisateur extends Model
{
    public $timestamps = false;
    protected $table = 'utilisateur';

    protected $primaryKey = 'id'; // Définir la clé primaire
    public $incrementing = false; // Désactiver l'auto-incrémentation
    protected $keyType = 'string'; // Spécifier que la clé est une string

    protected $fillable = [
        'id',
        'email',
        'nom',
        'prenom',
        'mot_de_passe',
        'date_naissance',
        'tentatives_connexion',
        'pdp'
    ];

    protected $hidden = ['mot_de_passe', 'tentatives_connexion'];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'id_utilisateur');
    }

    public function codePins(): HasMany
    {
        return $this->hasMany(CodePin::class, 'id_utilisateur');
    }
}
