<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Inscricao;
use App\Services\InscricaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InscricaoController extends Controller
{
    public function __construct(
        protected InscricaoService $inscricaoService
    ) {}

    public function index(Request $request)
    {
        return $this->inscricaoService->listDoUsuario($request->user());
    }

    public function store(Request $request, Evento $evento): JsonResponse
    {
        return $this->inscricaoService
            ->create($request->user(), $evento)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Inscricao $inscricao)
    {
        Gate::authorize('view', $inscricao);

        return $this->inscricaoService->show($inscricao);
    }

    public function destroy(Inscricao $inscricao): JsonResponse
    {
        Gate::authorize('delete', $inscricao);

        $this->inscricaoService->delete($inscricao);

        return response()->json(null, 204);
    }
}
