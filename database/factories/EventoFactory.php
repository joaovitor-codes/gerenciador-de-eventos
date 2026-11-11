<?php

namespace Database\Factories;

use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evento>
 */
class EventoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake() -> sentence(3),
            'descricao' => fake() -> paragraph(),
            'local' => fake() -> city(),
            'dt_evento' => fake() -> dateTimeBetween('now', '+3 months') -> format('Y-m-d'),
            'horario' => fake() -> time('H:i'),
            'qtd_max_participantes' => fake()->numberBetween(10, 200),
        ];
    }
}
