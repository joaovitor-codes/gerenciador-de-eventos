<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
public function index()
{
    $eventos = Evento::all();

    return EventoResource::collection($eventos);
}
        //

    /**
     * Store a newly created resource in storage.
     */
    
public function store(StoreEventoRequest $request)
{
    $dados = $request->validated();

    $evento = Evento::create($dados);

    return (new EventoResource($evento))
    ->response()
    ->setStatusCode(201);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $evento = Evento::findOrFail($id);

    return new EventoResource($evento);
}
        //
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $evento = Evento::findOrFail($id);

    $dados = $request->validate([
        'nome' => 'sometimes|required|string|max:255',
        'descricao' => 'sometimes|nullable|string',
        'local' => 'sometimes|required|string|max:255',
        'dt_evento' => 'sometimes|required|date|after_or_equal:today',
        'horario' => 'sometimes|required|date_format:H:i',
        'qtd_max_participantes' => 'sometimes|required|integer|min:1',
    ]);

    $evento->update($dados);

    return new EventoResource($evento);
}
    

    /**
     * Remove the specified resource from storage.
     */
public function destroy(string $id)
{
    $evento = Evento::findOrFail($id);

    $evento->delete();

    return response()->json(null, 204);
}
}