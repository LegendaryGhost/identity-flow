<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodePin extends Model
{
    use HasFactory;


    protected $table = 'code_pin';
    protected $primaryKey = 'id_code_pin';
    protected $fillable = [
        'code',
        'date_expiration',
        'id_utilisateur',
    ];
    public $timestamps = true;

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
