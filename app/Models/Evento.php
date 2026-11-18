<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'descricao', 'local', 'dt_evento', 'horario', 'qtd_max_participantes'
    ];

    protected $casts = [
        'dt_evento' => 'date',
    ];
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inscricoes(): HasMany
{
    return $this->hasMany(Inscricao::class);
}

public function participantes(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'inscricoes')->withTimestamps();
}

}
