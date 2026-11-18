<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventoTest extends TestCase
{
    use RefreshDatabase;

    private function dadosValidos(array $sobrescrever = []): array
    {
        return array_merge([
            'nome' => 'Semana de Tecnologia',
            'descricao' => 'Palestras e oficinas.',
            'local' => 'Auditório A',
            'dt_evento' => now()->addWeek()->format('Y-m-d'),
            'horario' => '19:30',
            'qtd_max_participantes' => 50,
        ], $sobrescrever);
    }

    public function test_listagem_de_eventos_e_publica_e_paginada(): void
    {
        Evento::factory()->count(3)->create();

        $this->getJson('/api/eventos')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'nome', 'vagas_restantes']], 'meta', 'links']);
    }

    public function test_consulta_de_evento_especifico(): void
    {
        $evento = Evento::factory()->create(['qtd_max_participantes' => 10]);

        $this->getJson("/api/eventos/{$evento->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $evento->id)
            ->assertJsonPath('data.nome', $evento->nome)
            ->assertJsonPath('data.vagas_restantes', 10);
    }

    public function test_consulta_de_evento_inexistente_retorna_404(): void
    {
        $this->getJson('/api/eventos/999')->assertNotFound();
    }

    public function test_usuario_autenticado_cadastra_evento_e_vira_dono(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/eventos', $this->dadosValidos())
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Semana de Tecnologia')
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('eventos', ['nome' => 'Semana de Tecnologia', 'user_id' => $user->id]);
    }

    public function test_cadastro_exige_apenas_nome_alem_de_data_horario_e_vagas(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/eventos', $this->dadosValidos(['descricao' => null, 'local' => null]))
            ->assertCreated();
    }

    public function test_cadastro_valida_campos(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/eventos', [
            'dt_evento' => now()->subDay()->format('Y-m-d'),
            'horario' => 'qualquer coisa',
            'qtd_max_participantes' => 0,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['nome', 'dt_evento', 'horario', 'qtd_max_participantes']);
    }

    public function test_visitante_nao_pode_cadastrar_evento(): void
    {
        $this->postJson('/api/eventos', $this->dadosValidos())->assertUnauthorized();

        $this->assertDatabaseCount('eventos', 0);
    }

    public function test_dono_pode_atualizar_evento(): void
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->for($user, 'criador')->create();
        Sanctum::actingAs($user);

        $this->patchJson("/api/eventos/{$evento->id}", ['nome' => 'Novo nome'])
            ->assertOk()
            ->assertJsonPath('data.nome', 'Novo nome');

        $this->assertDatabaseHas('eventos', ['id' => $evento->id, 'nome' => 'Novo nome']);
    }

    public function test_outro_usuario_nao_pode_atualizar_evento(): void
    {
        $evento = Evento::factory()->create(['nome' => 'Original']);
        Sanctum::actingAs(User::factory()->create());

        $this->patchJson("/api/eventos/{$evento->id}", ['nome' => 'Invadido'])->assertForbidden();

        $this->assertDatabaseHas('eventos', ['id' => $evento->id, 'nome' => 'Original']);
    }

    public function test_dono_pode_excluir_evento(): void
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->for($user, 'criador')->create();
        Sanctum::actingAs($user);

        $this->deleteJson("/api/eventos/{$evento->id}")->assertNoContent();

        $this->assertDatabaseMissing('eventos', ['id' => $evento->id]);
    }

    public function test_outro_usuario_nao_pode_excluir_evento(): void
    {
        $evento = Evento::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/eventos/{$evento->id}")->assertForbidden();

        $this->assertDatabaseHas('eventos', ['id' => $evento->id]);
    }
}
