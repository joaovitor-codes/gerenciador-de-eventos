<?php

namespace App\Services;

use App\Http\Resources\InscricaoResource;
use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InscricaoService
{
    public function create(User $user, Evento $evento): InscricaoResource
    {
        try {
            $inscricao = DB::transaction(function () use ($user, $evento) {
                $evento = Evento::whereKey($evento->id)->lockForUpdate()->firstOrFail();

                if ($evento->inscricoes()->where('user_id', $user->id)->exists()) {
                    throw ValidationException::withMessages([
                        'evento' => 'Você já está inscrito neste evento.',
                    ]);
                }

                if ($evento->inscricoes()->count() >= $evento->qtd_max_participantes) {
                    throw ValidationException::withMessages([
                        'evento' => 'Este evento já atingiu a lotação máxima.',
                    ]);
                }

                return $evento->inscricoes()->create(['user_id' => $user->id]);
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'evento' => 'Você já está inscrito neste evento.',
            ]);
        }

        return new InscricaoResource($inscricao->load(['evento' => fn ($query) => $query->withCount('inscricoes')]));
    }

    /**
     * Cancela a inscrição. Como a lotação é calculada pela contagem de
     * inscrições, removê-la libera automaticamente a vaga no evento.
     */
    public function delete(Inscricao $inscricao): void
    {
        DB::transaction(function () use ($inscricao) {
            Evento::whereKey($inscricao->evento_id)->lockForUpdate()->first();

            $inscricao->delete();
        });
    }
}
