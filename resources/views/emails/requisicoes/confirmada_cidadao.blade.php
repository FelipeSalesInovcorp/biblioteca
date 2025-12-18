@component('mail::message')
@php($capaCid = $capaCid ?? null)
# Requisição confirmada ✅

Olá, **{{ $cidadao->name }}**!

A sua requisição foi registada com sucesso.

@component('mail::panel')
**Número:** #{{ $requisicao->numero_sequencial }}  
**Livro:** {{ $livro->nome }}  
**ISBN:** {{ $livro->isbn }}  
**Data da requisição:** {{ $requisicao->data_requisicao->format('d/m/Y') }}  
**Data prevista de fim:** {{ $requisicao->data_prevista_fim->format('d/m/Y') }}
@endcomponent

@if($capaCid)
**Capa do livro:** 
    A capa do livro segue em anexo.
@endif


Obrigado,  
{{ config('app.name') }}
@endcomponent

