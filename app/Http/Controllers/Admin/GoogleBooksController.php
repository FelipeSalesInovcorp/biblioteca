<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Livros\ImportLivroFromGoogleBooks;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoogleBooksImportRequest;
use App\Http\Requests\Admin\GoogleBooksSearchRequest;
use App\Services\GoogleBooksService;

class GoogleBooksController extends Controller
{
    public function __construct(
        private readonly GoogleBooksService $googleBooks
    ) {}

    public function index(GoogleBooksSearchRequest $request)
    {
        $q = $request->validatedQuery();

        $results = null;
        if ($q !== '') {
            $results = $this->googleBooks->search($q); // ['totalItems' => int, 'items' => [...]]
        }

        return view('admin.googlebooks.index', compact('q', 'results'));
    }

    public function import(
        GoogleBooksImportRequest $request,
        ImportLivroFromGoogleBooks $importAction
    ) {
        $livro = $importAction->handle($request->googleVolumeId());

        return redirect()
            //->route('admin.livros.show', $livro) // ajusta para a tua route real
            ->route('admin.googlebooks.index', ['q' => request('q')])
            ->with('success', 'Livro importado com sucesso.');
    }
}
