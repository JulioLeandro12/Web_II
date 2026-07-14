<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $user = User::find($request->user_id);
        
        // Check if the user has any debit
        if ($user->debit > 0) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Este usuário possui um débito pendente de R$ ' .
                    number_format($user->debit, 2, ',', '.') .
                    '. O pagamento deve ser realizado antes de novos empréstimos.'
                );
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
        $borrowDate = Carbon::parse($borrowing->borrowed_at); // Get the borrowed_at date from the borrowing record
        $returnDate = Carbon::now(); // Get the current date and time for the return

        $daysBorrowed = $borrowDate->diffInDays($returnDate); // Calculate the number of days the book was borrowed

        $fine = 0; // Initialize fine variable

        // Use a transaction to ensure that both the fine and the returned_at update are applied atomically
        DB::transaction(function () use ($borrowing, $daysBorrowed, &$fine) {

            // Check if the book was borrowed for more than 15 days
            if ($daysBorrowed > 15) {

                $lateDays = $daysBorrowed - 15;

                $fine = $lateDays * 0.50;

                $user = $borrowing->user;

                $user->update([
                    'debit' => $user->debit + $fine
                ]);
            }

            // Update the borrowing record to mark the book as returned
            $borrowing->update([
                'returned_at' => now(),
            ]);
        });

        // Redirect with a message about the fine if applicable
        if ($fine > 0) {
            return redirect()
                ->route('books.show', $borrowing->book_id)
                ->with(
                    'success',
                    'Devolução registrada com sucesso. Multa aplicada: R$ ' .
                    number_format($fine, 2, ',', '.')
                );
        }

        // Redirect without a fine message if no fine was applied
        return redirect()
            ->route('books.show', $borrowing->book_id)
            ->with('success', 'Devolução registrada com sucesso.');
    }


    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();
    
        return view('users.borrowings', compact('user', 'borrowings'));
    }
}
