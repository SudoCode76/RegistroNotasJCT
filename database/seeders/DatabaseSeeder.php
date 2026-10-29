<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        User::query()->firstOrCreate(
            ['email' => 'docente@jct.test'],
            ['name' => 'Docente Demo', 'password' => 'password'],
        );

        $this->call(AcademicDemoSeeder::class);
    }
}
