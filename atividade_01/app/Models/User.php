<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password','role','debit'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    const ADMIN = 'admin';
    const BIBLIOTECARIO = 'bibliotecario';
    const CLIENTE = 'cliente';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'borrowings')
                    ->withPivot('id', 'borrowed_at', 'returned_at')
                    ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isBibliotecario(): bool
    {
        return $this->role === self::BIBLIOTECARIO;
    }

    public function isCliente(): bool
    {
        return $this->role === self::CLIENTE;
    }

    // Add a method to check if the user has any debit
    public function hasDebit(): bool
    {
        return $this->debit > 0;
    }

    // Add a method to add debit to the user
    public function addDebit(float $value): void
    {
        $this->increment('debit', $value);
    }

    // Add a method to clear the user's debit
    public function clearDebit(): void
    {
        $this->update([
            'debit' => 0
        ]);
    }
    
}

