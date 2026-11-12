<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'required|string|max:255',
            'dt_evento' => 'required|date|after_or_equal:today',
            'horario' => 'required',
            'qtd_max_participantes' => 'required|integer|min:1',
        ];
    }
}

