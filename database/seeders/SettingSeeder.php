<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'shift_configuration'],
            [
                'group' => 'notifications',
                'value' => json_encode([
                    'DIA' => [
                        'start' => '06:00',
                        'end' => '13:59',
                        'notifications' => ['08:00', '09:00', '10:00', '11:00', '12:00'],
                        'frequency' => 60
                    ],
                    'TARDE' => [
                        'start' => '14:00',
                        'end' => '21:59',
                        'notifications' => ['16:00', '17:00', '18:00', '19:00', '20:00'],
                        'frequency' => 60
                    ],
                    'NOCHE' => [
                        'start' => '22:00',
                        'end' => '05:59',
                        'notifications' => ['00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00'],
                        'frequency' => 30
                    ]
                ])
            ]
        );
    }
}
