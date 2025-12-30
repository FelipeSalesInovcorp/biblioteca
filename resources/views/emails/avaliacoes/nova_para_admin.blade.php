@component('mail::message')
# Nova avaliação pendente

Livro: **{{ $avaliacao->livro->titulo ?? '—' }}**  
Cidadão: **{{ $avaliacao->user->name ?? $avaliacao->user->email }}**  
Classificação: **{{ $avaliacao->classificacao }}/5**

@component('mail::button', ['url' => route('admin.avaliacoes.show', $avaliacao)])
Abrir detalhe da avaliação
@endcomponent

Obrigado,  
{{ config('app.name') }}
@endcomponent
File: resources/views/admin/avaliacoes/index.blade.php
