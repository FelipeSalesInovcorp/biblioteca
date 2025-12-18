@component('mail::message')
@php($capaCid = $capaCid ?? null)
# Nova requisição registada 📌

Foi criada uma nova requisição.

@component('mail::panel')
**Número:** #{{ $requisicao->numero_sequencial }}  
**Cidadão:** {{ $cidadao->name }} ({{ $cidadao->email }})  
**Foto/Avatar:** {{ $cidadao->profile_photo_url ?? '—' }}  
**Livro:** {{ $livro->nome }}  
**ISBN:** {{ $livro->isbn }}  
**Data da requisição:** {{ $requisicao->data_requisicao->format('d/m/Y') }}  
**Data prevista de fim:** {{ $requisicao->data_prevista_fim->format('d/m/Y') }}
@endcomponent

@if($capaCid)
**Capa do livro:** 
    A capa do livro segue em anexo.
@endif


{{ config('app.name') }}
@endcomponent

