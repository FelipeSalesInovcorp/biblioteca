@component('mail::message')
# Já pode deixar uma avaliação ⭐

A entrega do livro **{{ $requisicao->livro?->nome ?? '—' }}** foi confirmada.

Se quiser, pode agora deixar a sua avaliação na sua requisição.

@component('mail::button', ['url' => route('requisicoes.show', $requisicao)])
Avaliar requisição
@endcomponent

Obrigado,  
{{ config('app.name') }}
@endcomponent
