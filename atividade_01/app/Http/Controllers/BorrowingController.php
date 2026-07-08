<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing; 

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
      $openLoan = $book->loans()
    ->whereNull('return_date')
    ->exists();

if ($openLoan) {
    return redirect()
        ->back()
        ->with('error', 'Este livro já está emprestado.');
}
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if the book is already borrowed
        $borrowedBook = Borrowing::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($borrowedBook) {
            return redirect()
                ->back()
                ->with('error', 'Este livro já está emprestado.');
        }

        // Check if the user has already borrowed 5 books
        $borrowedBooks = Borrowing::where('user_id', $request->user_id)
            ->whereNull('returned_at')
            ->count();

        if ($borrowedBooks >= 5) {
            return redirect()
                     ->back()
                     ->with('error', 'Este usuário já possui o limite máximo de 5 livros emprestados.');
        }

        // Create a new borrowing record
        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function returnBook(Borrowing $borrowing)
    {
        $borrowing->update([
            'returned_at' => now(),
        ]);

        return redirect()->route('books.show', $borrowing->book_id)->with('success', 'Devolução registrada com sucesso.');
    }

    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();
    
        return view('users.borrowings', compact('user', 'borrowings'));
    }
}
