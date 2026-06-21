<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CamaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $camaras = [
            'IGNACIO MERINO', 'ÓVALO PUNTA ARENAS', 'AV. G', 'AV. H COLEGIOS', 
            'CLINICA TRESA', 'NIÑO HÉROE', 'LA PARADA', 'INTERCOM LA PARADA', 
            'MORGUE', 'MONTERO', 'PALACIO MUNICIPAL', 'PIPOS', 
            'MAVILA ALTA', 'IGLESIA LA INMACULADA', 'ZONA DE BANCOS', 'CURACAO BCP', 
            'CAJA PIURA', 'PARQUE 10', 'PARQUE 17 Y 14', 'PARQUE 10', 
            'GRIFO SAN MARTÍN', 'SENATI', 'TOYOTA', 'INTERCOM. TOYOTA', 
            'POSTE 06 TOYOTA', 'PTZ POSTE INMACULADA', 'INTERCOM. INMACULADA', 'POSTE 04 MAVILA CENTRO', 
            'INTERCOM. MAVILA', 'LRP PUNTA ARENAS', 'TRONCOS', 'SALIDA A LOBITOS', 
            'AV. B', 'PLAZA GRAU', 'PLAZUELA', 'LPR P.A', 
            'FONAVI', 'SAN JUDAS', 'ACAPULCO', 'LAS PEÑITAS', 
            'MERCADO ACAPULCO', 'ENACE I', 'MINSA', 'SAN MARTÍN', 
            'NUEVO TALARA', 'COLEGIO POLITÉCNICO', 'POLVADERA', 'LOS VENCEDORES', 
            'MERCADO TALARA ALTA', 'MERCADO TALARA ALTA INTER', 'CEMENTERIO LA INMACULADA', 'CRISTO REY', 
            'PARADERO 13', 'LPR OVALO URBA', 'IE. DOMINGO SAVIO', 'IE. FEDERICO VILLARREAL', 
            'ESCUELA 13 PARQUE', 'IE. SAN SEBASTIÁN', 'COLEGIO IGNACIO MERINO', 'COLEGIO MÁRTIRES DEL PETRÓLEO', 
            'PARQUE PIURA', 'LPR PIROTECNIA', 'ÓVALO VÍCTOR RAÚL', 'PARQUE EL PESCADOR', 
            'A.H. JESÚS MARÍA', 'A.H. SANTA RITA', 'PARADERO 20', 'COLEGIO 13', 
            'COLA DE GATO', 'CUADRADO DEL AGUA', 'GRUTA JORGE CHÁVEZ', 'PILAR NORES', 
            'CHATARREROS', 'MARIO AGUIRRE', 'CORPAC', '07 DE JUNIO', 
            'PTZ POSTE ÓVALO DE LA URBA', 'INTERCOM ÓVALO URBA', 'POSTE 06 GRUTA JORGE CHÁVEZ', 'INTERCOM GRUTA JORGE CHÁVEZ', 
            'POSTE 07 AEROPUERTO', 'INTERCOM AEROPUERTO', 'POSTE 09 VÍCTOR RAÚL', 'INTERCOM VÍCTOR RAÚL', 
            'SACOBSA', 'GRIFO CHALLE N.T', 'NEGREIROS-LUCIANO', 'TANQUE ELEVADO', 
            'ENACE II ANTENA', 'POSTE OVALO URBA'
        ];

        foreach ($camaras as $nombre) {
            \App\Models\Camara::create([
                'nombre' => $nombre,
                'activa' => true
            ]);
        }
    }
}
