<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

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
