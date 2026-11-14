<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Usa o withCount('inscricoes') quando carregado; senão, conta no banco.
        $participantesInscritos = $this->inscricoes_count ?? $this->inscricoes()->count();

        $vagasRestantes = max(
            0,
            $this->qtd_max_participantes - $participantesInscritos
        );

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'local' => $this->local,
            'dt_evento' => $this->dt_evento,
            'horario' => $this->horario,
            'qtd_max_participantes' => $this->qtd_max_participantes,
            'vagas_restantes' => $vagasRestantes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}