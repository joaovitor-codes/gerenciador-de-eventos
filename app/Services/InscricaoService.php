<?php

namespace App\Services;

use App\Http\Resources\InscricaoResource;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InscricaoService
{
    /**
     * Lista apenas as inscrições do usuário informado, com os dados do evento.
     */
    public function listByUser(User $user): AnonymousResourceCollection
    {
        $inscricoes = $user->inscricoes()
            ->with(['evento' => fn ($query) => $query->withCount('inscricoes')])
            ->latest()
            ->get();

        return InscricaoResource::collection($inscricoes);
    }

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
}
