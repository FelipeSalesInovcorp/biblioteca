<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

// Teste para criação de requisições de livros
it('permite criar uma requisição com um livro válido', function () {
    Mail::fake();

    $cidadao = User::factory()->cidadao()->create();
    $livro = Livro::factory()->comStock(1)->create();

    $response = $this->actingAs($cidadao)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
    ]);

    // normalmente redireciona após criar
    $response->assertStatus(302);

    $this->assertDatabaseHas('requisicoes', [
        'user_id' => $cidadao->id,
        'livro_id' => $livro->id,
        'data_entrega_real' => null,
    ]);

    $requisicao = Requisicao::where('user_id', $cidadao->id)
        ->where('livro_id', $livro->id)
        ->latest('id')
        ->first();

    expect($requisicao)->not->toBeNull();
    expect($requisicao->numero_sequencial)->toBeInt();
});

// Teste para criação de requisição sem fornecer livro_id
it('não permite criar requisição sem livro_id', function () {
    Mail::fake();

    $cidadao = User::factory()->cidadao()->create();

    $response = $this->actingAs($cidadao)->post(route('requisicoes.store'), [
        // livro_id ausente
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['livro_id']);

    $this->assertDatabaseCount('requisicoes', 0);
});

// Teste para criação de requisição com livro inexistente
it('não permite criar requisição com livro inexistente', function () {
    Mail::fake();

    $cidadao = User::factory()->cidadao()->create();

    $response = $this->actingAs($cidadao)->post(route('requisicoes.store'), [
        'livro_id' => 999999,
    ]);

    // normalmente dá 302 por causa do findOrFail
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['livro_id']);

    $this->assertDatabaseCount('requisicoes', 0);
});

// Teste para confirmar a entrega de uma requisição
it('permite confirmar a entrega de uma requisição', function () {
    Mail::fake();
    Carbon::setTestNow('2026-01-10 10:00:00');

    $cidadao = User::factory()->cidadao()->create();
    $livro = Livro::factory()->comStock(1)->create();

    // cria requisição (via POST como no fluxo real)
    $this->actingAs($cidadao)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
    ])->assertStatus(302);

    $requisicao = Requisicao::where('user_id', $cidadao->id)
        ->where('livro_id', $livro->id)
        ->latest('id')
        ->first();

    expect($requisicao)->not->toBeNull();
    expect($requisicao->data_entrega_real)->toBeNull();

    // avançar o tempo 2 dias para termos dias_decorridos > 0
    Carbon::setTestNow('2026-01-12 10:00:00');

    // confirmar entrega como admin
    //$response = $this->actingAs($cidadao)->post(route('requisicoes.confirmEntrega', $requisicao));

    $admin = User::factory()->admin()->create();
    $response = $this->actingAs($admin)->post(route('requisicoes.confirmEntrega', $requisicao));

    $response->assertStatus(302);

    $requisicao->refresh();

    expect($requisicao->data_entrega_real)->not->toBeNull();
    expect($requisicao->dias_decorridos)->not->toBeNull();

    // log de confirmação
    $this->assertDatabaseHas('logs', [
        'module' => 'Requisicoes',
        'object_id' => $requisicao->id,
        'user_id' => $cidadao->id,
    ]);

    $log = Log::where('module', 'Requisicoes')
        ->where('object_id', $requisicao->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->change)->toContain('Confirmou entrega da requisição');
});

// Teste para criação de requisição quando o livro não tem stock
it('não permite criar requisição quando o livro não tem stock', function () {
    Mail::fake();

    $cidadao = User::factory()->cidadao()->create();

    // livro sem stock
    $livro = Livro::factory()->comStock(0)->create();

    $response = $this->actingAs($cidadao)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
    ]);

    // redireciona com erro
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['livro_id']);

    // nenhuma requisição criada
    $this->assertDatabaseCount('requisicoes', 0);
});

// Teste para listar apenas as requisições do utilizador autenticado
it('lista apenas as requisições do utilizador autenticado em minhas-requisicoes', function () {
    Mail::fake();

    $u1 = User::factory()->cidadao()->create();
    $u2 = User::factory()->cidadao()->create();

    $livro1 = Livro::factory()->comStock(1)->create();
    $livro2 = Livro::factory()->comStock(1)->create();

    // u1 cria 1 requisição
    $this->actingAs($u1)->post(route('requisicoes.store'), [
        'livro_id' => $livro1->id,
    ])->assertStatus(302);

    // u2 cria 1 requisição
    $this->actingAs($u2)->post(route('requisicoes.store'), [
        'livro_id' => $livro2->id,
    ])->assertStatus(302);

    // u1 consulta "minhas-requisicoes"
    $response = $this->actingAs($u1)->get(route('requisicoes.minhas'));
    $response->assertStatus(200);

    // deve ver o livro do u1
    $response->assertSee($livro1->nome);

    // não deve ver o livro do u2
    $response->assertDontSee($livro2->nome);
});


