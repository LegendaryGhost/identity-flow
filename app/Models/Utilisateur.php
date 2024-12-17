<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Utilisateur extends Model
{
    use HasFactory;

    protected $table = "utilisateur";
    protected $primaryKey = "id_utilisateur";
    protected $fillable = [
        'email',
        'nom',
        'prenom',
        'mot_de_passe',
        'date_naissance',
    ];
    public $timestamps = true;

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'id_utilisateur');
    }

    public function codePins(): HasMany
    {
        return $this->hasMany(CodePin::class, 'id_utilisateur');
    }

}
