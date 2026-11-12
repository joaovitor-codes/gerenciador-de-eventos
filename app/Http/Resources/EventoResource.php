<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Enquanto não houver inscrições, todas as vagas estão disponíveis.
        $participantesInscritos = 0;

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