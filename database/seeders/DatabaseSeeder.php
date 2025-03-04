<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Sulthan Alif Hayatyo',
            'email' => 'sulthanalif45@gmail.com',
            'password' => 'password'
        ]);

        User::factory()->create([
            'name' => 'Latifa Nuraliza Diva',
            'email' => 'latifanuraliza07@gmail.com',
            'password' => 'password'
        ]);


        $categories = [
            ['code' => 001, 'name' => 'Pemasukan', 'description' => 'Pemasukan'],
            ['code' => 002, 'name' => 'Pengeluaran', 'description' => 'Pengeluaran'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

    }
}
