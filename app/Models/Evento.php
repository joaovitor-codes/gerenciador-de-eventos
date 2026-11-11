<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'descricao', 'local', 'dt_evento', 'horario', 'qtd_max_participantes'
    ];

    protected $casts = [
        'dt_evento' => 'date',
    ];
}
