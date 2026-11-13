<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Evento;
use App\Services\EventoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(
        protected EventoService $eventoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->eventoService->list($request->integer('por_pagina', 15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventoRequest $request): JsonResponse
    {
        return $this->eventoService
            ->create($request->validated())
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Evento $evento)
    {
        return $this->eventoService->show($evento);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventoRequest $request, Evento $evento)
    {
        return $this->eventoService->update($evento, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evento $evento): JsonResponse
    {
        $this->eventoService->delete($evento);

        return response()->json(null, 204);
    }
}
