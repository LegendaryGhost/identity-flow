<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Utilisateur extends Model
{
    public $timestamps = false;

    protected $table = 'utilisateur';

    protected $fillable = [
        'email',
        'nom',
        'prenom',
        'mot_de_passe',
        'date_naissance',
        'tentatives_connexion'
    ];

    protected $hidden = ['mot_de_passe'];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'id_utilisateur');
    }

    public function codePins(): HasMany
    {
        return $this->hasMany(CodePin::class, 'id_utilisateur');
    }
}
