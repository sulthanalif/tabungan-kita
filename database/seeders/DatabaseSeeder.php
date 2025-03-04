<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Sulthan Alif Hayatyo',
            'email' => 'sulthanalif45@gmail.com',
            'password' => 'password'
        ]);

        User::create([
            'name' => 'Latifa Nuraliza Diva',
            'email' => 'latifanuraliza07@gmail.com',
            'password' => 'password'
        ]);


        $categories = [
            ['code' => 001, 'name' => 'Pemasukan', 'description' => 'Pemasukan'],
            ['code' => 002, 'name' => 'Pengeluaran', 'description' => 'Pengeluaran'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

    }
}
