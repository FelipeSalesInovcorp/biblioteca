@component('mail::message')
# Decisão sobre a sua avaliação

Livro: **{{ $avaliacao->livro->nome ?? '—' }}**  
Estado: **{{ $avaliacao->estado }}**

@if ($avaliacao->estado === 'recusada' && $avaliacao->motivo_recusa)
**Motivo da recusa:**  
{{ $avaliacao->motivo_recusa }}
@endif

Obrigado,  
{{ config('app.name') }}
@endcomponent
File: resources/views/emails/avaliacoes/decisao_para_cidadao.blade.php
