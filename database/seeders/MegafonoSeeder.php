<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Megafono;

class MegafonoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $megafonos = [
            ['nombre' => 'MARIO AGUIRRE', 'codigo' => '264'],
            ['nombre' => 'MAVILA APRA', 'codigo' => '217'],
            ['nombre' => 'MAX CORNEJO PACORA', 'codigo' => '205'],
            ['nombre' => 'MERCADO ACAPULCO', 'codigo' => '210'],
            ['nombre' => 'MONTERO', 'codigo' => '222'],
            ['nombre' => 'MORGUE', 'codigo' => '221'],
            ['nombre' => 'MUELLE UNO', 'codigo' => '212'],
            ['nombre' => 'NEGREIROS-LUCIANO', 'codigo' => '267'],
            ['nombre' => 'NIÑO HÉROE', 'codigo' => '237'],
            ['nombre' => 'OVALO PUNTA ARENAS', 'codigo' => '235'],
            ['nombre' => 'OVALO URBA', 'codigo' => '246'],
            ['nombre' => 'PALACIO MUNICIPAL', 'codigo' => '220'],
            ['nombre' => 'PARADERO 20', 'codigo' => '253'],
            ['nombre' => 'PARCELA 25', 'codigo' => '201'],
            ['nombre' => 'PARQUE 10', 'codigo' => '224'],
            ['nombre' => 'PARQUE 16', 'codigo' => '227'],
            ['nombre' => 'PARQUE 17 Y 14', 'codigo' => '223'],
            ['nombre' => 'PARQUE 28 JULIO', 'codigo' => '250'],
            ['nombre' => 'PECATA', 'codigo' => '226'],
            ['nombre' => 'PILAR NORES', 'codigo' => '261'],
            ['nombre' => 'PIPOS', 'codigo' => '219'],
            ['nombre' => 'PLAZUELA CÁCERES', 'codigo' => '230'],
            ['nombre' => 'PLAZUELA PESCADOR', 'codigo' => '204'],
            ['nombre' => 'PLAZUELA QUIÑONES', 'codigo' => '256'],
            ['nombre' => 'POLITÉCNICO', 'codigo' => '207'],
            ['nombre' => 'POLLERÍA MARUJA', 'codigo' => '225'],
            ['nombre' => 'POSTA CONO NORTE', 'codigo' => '202'],
            ['nombre' => 'POSTA QUIÑONES', 'codigo' => '254'],
            ['nombre' => 'POSTE 04 MAVILA CENTRO CÍVICO', 'codigo' => '***'],
            ['nombre' => 'POSTE 06 GRUTA JORGE CHÁVEZ', 'codigo' => '***'],
            ['nombre' => 'POSTE 07 AEROPUERTO', 'codigo' => '***'],
            ['nombre' => 'POSTE 08 TOYOTA', 'codigo' => '***'],
            ['nombre' => 'POSTE 09 VÍCTOR RAÚL', 'codigo' => '***'],
            ['nombre' => 'PTZ POSTE 10 PLAZUELA PESCADOR', 'codigo' => '***'],
            ['nombre' => 'PTZ POSTE INMACULADA', 'codigo' => '***'],
            ['nombre' => 'PTZ POSTE OVALO DE LA URBA', 'codigo' => '***'],
            ['nombre' => 'PUENTE VÍCTOR RAÚL', 'codigo' => '***'],
            ['nombre' => 'PUENTE YALE', 'codigo' => '228'],
            ['nombre' => 'SACOBSA', 'codigo' => '265'],
            ['nombre' => 'SALIDA A LOBITOS', 'codigo' => '200'],
            ['nombre' => 'SAPISA', 'codigo' => '249'],
            ['nombre' => 'SENATI', 'codigo' => '239'],
            ['nombre' => 'TANQUE ELEVADO', 'codigo' => '268'],
            ['nombre' => 'TANQUE VÍCTOR RAÚL', 'codigo' => '252'],
            ['nombre' => 'TOYOTA', 'codigo' => '240'],
            ['nombre' => 'TRONCOS', 'codigo' => '245'],
            ['nombre' => 'ZONA DE BANCOS', 'codigo' => '216'],
        ];

        foreach ($megafonos as $megafono) {
            Megafono::updateOrCreate(
                ['nombre' => $megafono['nombre']],
                ['codigo' => $megafono['codigo']]
            );
        }
    }
}
