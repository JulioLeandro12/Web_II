@extends('layouts.app')

@can('manageDebits', App\Models\User::class)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.debts') }}">
            Controle de Multas
        </a>
    </li>
@endcan

@section('content')
<div class="container">

    <h2>Usuários com Débitos</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($users->isEmpty())

        <div class="alert alert-success">
            Nenhum usuário possui débitos.
        </div>

    @else

    <table class="table table-striped">

        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Débito</th>
                <th>Ação</th>
            </tr>
        </thead>

        <tbody>

        @foreach($users as $user)

            <tr>

                <td>{{ $user->name }}</td>

                <td>{{ $user->email }}</td>

                <td>
                    <strong>
                        R$ {{ number_format($user->debit, 2, ',', '.') }}
                    </strong>
                </td>

                <td>

                    <form action="{{ route('users.clearDebit', $user) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <button class="btn btn-success btn-sm">
                            Confirmar Pagamento
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    @endif

</div>
@endsection