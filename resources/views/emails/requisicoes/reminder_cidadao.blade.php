@component('mail::message')
# Lembrete de entrega 📅

Olá, **{{ $cidadao->name }}**!

Este é um lembrete de que a data prevista de fim da sua requisição é **amanhã**.

@component('mail::panel')
**Número:** #{{ $requisicao->numero_sequencial }}  
**Livro:** {{ $livro->nome }}  
**ISBN:** {{ $livro->isbn }}  
**Data prevista de fim:** {{ $requisicao->data_prevista_fim->format('d/m/Y') }}
@endcomponent

@if($livro->imagem_capa)
A capa do livro segue em anexo.
@endif

Obrigado,  
{{ config('app.name') }}
@endcomponent
