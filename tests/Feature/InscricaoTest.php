<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InscricaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_se_inscreve_em_evento(): void
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create(['qtd_max_participantes' => 5]);
        Sanctum::actingAs($user);

        $this->postJson("/api/eventos/{$evento->id}/inscricoes")
            ->assertCreated()
            ->assertJsonPath('data.evento.id', $evento->id)
            ->assertJsonPath('data.evento.vagas_restantes', 4);

        $this->assertDatabaseHas('inscricoes', ['user_id' => $user->id, 'evento_id' => $evento->id]);
    }

    public function test_visitante_nao_pode_se_inscrever(): void
    {
        $evento = Evento::factory()->create();

        $this->postJson("/api/eventos/{$evento->id}/inscricoes")->assertUnauthorized();

        $this->assertDatabaseCount('inscricoes', 0);
    }

    public function test_nao_permite_inscricao_duplicada(): void
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson("/api/eventos/{$evento->id}/inscricoes")->assertCreated();

        $this->postJson("/api/eventos/{$evento->id}/inscricoes")
            ->assertUnprocessable()
            ->assertJsonPath('errors.evento.0', 'Você já está inscrito neste evento.');

        $this->assertDatabaseCount('inscricoes', 1);
    }

    public function test_nao_permite_inscricao_em_evento_lotado(): void
    {
        $evento = Evento::factory()->create(['qtd_max_participantes' => 1]);
        $evento->inscricoes()->create(['user_id' => User::factory()->create()->id]);

        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/eventos/{$evento->id}/inscricoes")
            ->assertUnprocessable()
            ->assertJsonPath('errors.evento.0', 'Este evento já atingiu a lotação máxima.');

        $this->assertDatabaseCount('inscricoes', 1);
    }

    public function test_inscricao_em_evento_inexistente_retorna_404(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/eventos/999/inscricoes')->assertNotFound();
    }
}
