<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        FakerFactory::create()->unique(true);
        $this->call([
            CategorySeeder::class,
            AuthorPublisherBookSeeder::class,
            UserBorrowingSeeder::class, // seed de usuários e empréstimos
            AdminSeeder::class, // seed de administrador
        ]);
    }
}