@component('mail::message')
# Olá, {{ $encomenda->user->name }} 👋

A tua encomenda **#{{ $encomenda->id }}** foi paga com sucesso.

@component('mail::panel')
**Entrega:** {{ $encomenda->nome_entrega }}  
{{ $encomenda->morada }}  
{{ $encomenda->codigo_postal }} — {{ $encomenda->localidade }}
@endcomponent

## Itens
@foreach($encomenda->items as $item)
- **{{ $item->livro?->nome ?? 'Livro' }}** — {{ $item->quantidade }} x {{ number_format((float)$item->preco_unitario, 2, ',', '.') }} €
@endforeach

**Total:** {{ number_format((float)$encomenda->total, 2, ',', '.') }} €

Obrigado,  
{{ config('app.name') }}
@endcomponent

