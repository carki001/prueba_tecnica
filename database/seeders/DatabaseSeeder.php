<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'admin@example.com',
            'password' => Hash::make('1_very_hard_passphrase'),
            'role' => 'admin',
            'preferred_language' => 'es', //possible es, cs, en
        ]);

        DB::table('roles')->insert([
            'nombre' => 'Profesional de proyectos - Desarrollador',
        ]);

        DB::table('roles')->insert([
            'nombre' => 'Gerente estratégico',
        ]);

        DB::table('roles')->insert([
            'nombre' => 'Auxiliar administrativo',
        ]);

        DB::table('areas')->insert([
            'nombre' => 'Ventas',
        ]);

        DB::table('areas')->insert([
            'nombre' => 'Calidad',
        ]);

        DB::table('areas')->insert([
            'nombre' => 'Producción',
        ]);
    }
}
