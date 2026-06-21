<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::find(1);
        if ($admin) {
            $admin->update([
                'username' => 'admin',
                'role' => 'admin',
                'password' => 'admin123', // Esto se hasheará automáticamente con el driver por defecto (Argon2id)
                'security_question' => '¿Cuál es el nombre de tu primera mascota?',
                'security_answer' => 'admin', // También se hasheará
            ]);
            $this->command->info('Usuario administrador actualizado correctamente.');
        } else {
            User::create([
                'name' => 'Administrador',
                'username' => 'admin',
                'email' => 'admin@ejemplo.com',
                'password' => 'admin123',
                'role' => 'admin',
                'security_question' => '¿Cuál es el nombre de tu primera mascota?',
                'security_answer' => 'admin',
            ]);
            $this->command->info('Usuario administrador creado correctamente.');
        }
    }
}
