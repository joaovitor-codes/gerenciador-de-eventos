<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string',
            'local' => 'sometimes|nullable|string|max:255',
            'dt_evento' => 'sometimes|required|date|after_or_equal:today',
            'horario' => 'sometimes|required|date_format:H:i',
            'qtd_max_participantes' => 'sometimes|required|integer|min:1',
        ];
    }
}
