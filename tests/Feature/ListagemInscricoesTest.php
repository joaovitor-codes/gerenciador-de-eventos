<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListagemInscricoesTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nao_autenticado_nao_pode_listar(): void
    {
        $this->getJson('/api/minhas-inscricoes')->assertUnauthorized();
    }

    public function test_usuario_lista_apenas_as_proprias_inscricoes(): void
    {
        $user = User::factory()->create();
        $outro = User::factory()->create();

        $eventoDoUsuario = Evento::factory()->create();
        $eventoDoOutro = Evento::factory()->create();

        $inscricao = $eventoDoUsuario->inscricoes()->create(['user_id' => $user->id]);
        $eventoDoOutro->inscricoes()->create(['user_id' => $outro->id]);

        Sanctum::actingAs($user);

        $this->getJson('/api/minhas-inscricoes')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $inscricao->id)
            ->assertJsonPath('data.0.evento.id', $eventoDoUsuario->id);
    }

    public function test_listagem_retorna_dados_do_evento(): void
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        $evento->inscricoes()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->getJson('/api/minhas-inscricoes')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'evento' => ['id', 'nome', 'local', 'dt_evento', 'horario'], 'created_at'],
                ],
            ])
            ->assertJsonPath('data.0.evento.nome', $evento->nome)
            ->assertJsonPath('data.0.evento.local', $evento->local);
    }

    public function test_usuario_sem_inscricoes_recebe_lista_vazia(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/minhas-inscricoes')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
