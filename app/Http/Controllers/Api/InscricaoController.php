<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Services\InscricaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    public function __construct(
        protected InscricaoService $inscricaoService
    ) {}

    public function store(Request $request, Evento $evento): JsonResponse
    {
        return $this->inscricaoService
            ->create($request->user(), $evento)
            ->response()
            ->setStatusCode(201);
    }
}
