<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CancelamentoInscricaoTest extends TestCase
{
    use RefreshDatabase;

    private function criarInscricao(User $user, Evento $evento): Inscricao
    {
        return $evento->inscricoes()->create(['user_id' => $user->id]);
    }

    public function test_usuario_pode_cancelar_propria_inscricao(): void
    {
        $user = User::factory()->create();
        $inscricao = $this->criarInscricao($user, Evento::factory()->create());

        Sanctum::actingAs($user);

        $this->deleteJson("/api/inscricoes/{$inscricao->id}")->assertNoContent();

        $this->assertDatabaseMissing('inscricoes', ['id' => $inscricao->id]);
    }

    public function test_usuario_nao_autenticado_nao_pode_cancelar(): void
    {
        $inscricao = $this->criarInscricao(User::factory()->create(), Evento::factory()->create());

        $this->deleteJson("/api/inscricoes/{$inscricao->id}")->assertUnauthorized();

        $this->assertDatabaseHas('inscricoes', ['id' => $inscricao->id]);
    }

    public function test_usuario_nao_pode_cancelar_inscricao_de_outro(): void
    {
        $inscricao = $this->criarInscricao(User::factory()->create(), Evento::factory()->create());

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/inscricoes/{$inscricao->id}")->assertForbidden();

        $this->assertDatabaseHas('inscricoes', ['id' => $inscricao->id]);
    }

    public function test_cancelar_inscricao_inexistente_retorna_404(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson('/api/inscricoes/999')->assertNotFound();
    }

    public function test_cancelamento_libera_vaga_no_evento(): void
    {
        $evento = Evento::factory()->create(['qtd_max_participantes' => 1]);
        $primeiro = User::factory()->create();
        $segundo = User::factory()->create();
        $inscricao = $this->criarInscricao($primeiro, $evento);

        Sanctum::actingAs($segundo);
        $this->postJson("/api/eventos/{$evento->id}/inscricoes")->assertUnprocessable();

        Sanctum::actingAs($primeiro);
        $this->deleteJson("/api/inscricoes/{$inscricao->id}")->assertNoContent();

        Sanctum::actingAs($segundo);
        $this->postJson("/api/eventos/{$evento->id}/inscricoes")->assertCreated();
    }
}
