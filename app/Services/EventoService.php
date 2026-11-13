<?php

namespace App\Services;

use App\Http\Resources\EventoResource;
use App\Models\Evento;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EventoService
{
    public function list(int $porPagina = 15): AnonymousResourceCollection
    {
        if ($porPagina < 1) {
            throw new BadRequestHttpException('Quantidade por página deve ser maior que 0');
        }

        return EventoResource::collection(Evento::paginate($porPagina));
    }

    public function show(Evento $evento): EventoResource
    {
        return new EventoResource($evento);
    }

    public function create(array $dados): EventoResource
    {
        $evento = Evento::create($dados);

        return new EventoResource($evento);
    }

    public function update(Evento $evento, array $dados): EventoResource
    {
        $evento->update($dados);

        return new EventoResource($evento);
    }

    public function delete(Evento $evento): void
    {
        $evento->delete();
    }
}
