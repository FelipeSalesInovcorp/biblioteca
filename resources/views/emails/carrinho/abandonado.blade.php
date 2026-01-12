@component('mail::message')
# Olá, {{ $carrinho->user->name }} 👋

Notámos que adicionaste livros ao teu carrinho, mas ainda não finalizaste a encomenda.

@component('mail::panel')
@foreach($carrinho->items as $item)
- **{{ $item->livro->nome ?? 'Livro' }}** — {{ $item->quantidade }} x {{ number_format((float)$item->preco_unitario, 2, ',', '.') }} €
@endforeach
@endcomponent

@component('mail::button', ['url' => route('carrinho.index')])
Voltar ao carrinho
@endcomponent

Obrigado,  
{{ config('app.name') }}
@endcomponent
