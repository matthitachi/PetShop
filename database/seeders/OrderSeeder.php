<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()->count(rand(50, 300))->create([
            'user_id' => User::where('is_admin', 0)->inRandomOrder()->first()->id,
        ]);
    }
}
