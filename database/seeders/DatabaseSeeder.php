<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $userRole = Role::firstOrCreate(['name' => 'Usuario']);
        $admin = User::firstOrCreate(
          ['email' => 'admin@promaq.com'],
          [
            'name' => 'Kathy',
            'password' => bcrypt('Admin2025'),
          ]);

        $admin->assignRole($adminRole);
        $user = User::firstOrCreate(
          ['email' => 'usuario@promaq.com'],
          [
            'name' => 'Usuario',
            'password' => bcrypt('Usuario2025'),
          ]);
        $user->assignRole($userRole);
        Setting::firstOrCreate(
            ['nit' => 'C/F'], // Buscar por NIT
            [
                'razonSocial' => 'PROMAQ I+D',
                'gerente' => 'Nombre del Gerente',
                'celular' => '44444444-77777777',
                'email' => 'info@promaq.com',
                'direccion' => 'Dirección de la empresa',
                'cod_orden' => 'OT',
                'cod_empleado' => 'CE',
                'cod_factura' => '0',
                'cod_cotizacion' => 'COT',
                'iva' => 13,
                'estado' => 1,
                'created_by' => User::first()?->id,
            ]
        );
        $this->command->info('Usuarios y roles creados exitosamente.');
    }
}
