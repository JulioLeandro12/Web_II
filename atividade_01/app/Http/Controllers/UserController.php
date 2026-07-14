<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::paginate(10); // Paginação para 10 usuários por página
        return view('users.index', compact('users'));
    }

    public function show(\App\Models\User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(\App\Models\User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $user->update($request->only('name', 'email'));

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    // listar os usuários com débitos
    public function debts()
    {
        $this->authorize('manageDebits', User::class);

        $users = User::where('debit', '>', 0)
            ->orderBy('name')
            ->get();

        return view('users.debts', compact('users'));
        // return var_dump($users); // Apenas para depuração
    }

    // limpar o débito de um usuário
    public function clearDebit(User $user)
    {
        $this->authorize('manageDebits', User::class);

        $user->update([
            'debit' => 0
        ]);

        return redirect()
            ->route('users.debts')
            ->with('success', 'Débito quitado com sucesso.');
    }

}