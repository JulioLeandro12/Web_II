<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        // Carregar os livros com autores usando eager loading e paginação
        $books = Book::with('author')->paginate(20);
    
        return view('books.index', compact('books'));

    }

    // Formulário com input de ID
    public function createWithId()
    {
        $this->authorize('create', Book::class);

        return view('books.create-id');
    }

    // Salvar livro com input de ID
    public function storeWithId(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'publisher_id' => 'required|exists:publishers,id',
        //     'author_id' => 'required|exists:authors,id',
        //     'category_id' => 'required|exists:categories,id',
        // ]);

        // Book::create($request->all());

        // return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');

        $this->authorize('create', Book::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'pages' => 'required|integer|min:1',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Livro cadastrado com sucesso.');

    }

    // Formulário com input select
    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        // $this->authorize('create', Book::class);

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'pages' => 'required|integer|min:1',
        //     'publisher_id' => 'required|exists:publishers,id',
        //     'author_id' => 'required|exists:authors,id',
        //     'category_id' => 'required|exists:categories,id',
        //     'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        // ]);

        // if ($request->hasFile('cover_image')) {
        //     $path = $request->file('cover_image')->store('covers', 'public');
        //     $request->merge(['cover_image' => $path]);
        // }

        // Book::create($request->all());

        // return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');

        // $this->authorize('create', Book::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'pages' => 'required|integer|min:1',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Livro cadastrado com sucesso.');
    }

    public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'publisher_id' => 'required|exists:publishers,id',
        //     'author_id' => 'required|exists:authors,id',
        //     'category_id' => 'required|exists:categories,id',
        // ]);

        // $book->update($request->all());

        // return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso.');

        $this->authorize('update', $book);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            // Deletar imagem antiga
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Salvar nova imagem
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso.');

    }

    public function show(Book $book)
    {
        // Carregando autor, editora e categoria do livro com eager loading
        $book->load(['author', 'publisher', 'category']);
    
        // Carregar todos os usuários para o formulário de empréstimo
        $users = User::all();
    
        return view('books.show', compact('book','users'));
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso.');
    }


}
