<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportarCamarasHikcentral extends Command
{
    protected $signature = 'camaras:importar {archivo}';
    protected $description = 'Importa la lista de cámaras desde un CSV exportado de HikCentral';

    public function handle()
    {
        $archivo = $this->argument('archivo');

        if (!file_exists($archivo)) {
            $this->error("❌ El archivo $archivo no existe.");
            return 1;
        }

        $this->info("📄 Importando cámaras desde $archivo...");

        // Leer el CSV de entrada
        $file = fopen($archivo, 'r');
        if (!$file) {
            $this->error("❌ No se pudo abrir el archivo.");
            return 1;
        }

        $camaras = [];
        $cabecera = fgetcsv($file); // Leer la primera línea (cabecera)

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($row) || count($row) < 3) continue;

            // Limpieza de tabulaciones y espacios
            $alias = trim(str_replace("\t", "", $row[0]));
            $ip = trim(str_replace("\t", "", $row[1]));
            $puerto = intval(trim(str_replace("\t", "", $row[2])));

            if (empty($ip) || $ip === 'Device Address') continue;

            $camaras[] = [$alias, $ip, $puerto, 'ONLINE']; // Estado inicial ONLINE
            $this->line("→ $alias ($ip:$puerto)");
        }

        fclose($file);

        // Escribir el CSV nuevo
        $rutaDestino = storage_path('app/cameras.csv');
        $fileDestino = fopen($rutaDestino, 'w');
        fputcsv($fileDestino, ['Alias', 'IP', 'Puerto', 'Estado']);

        foreach ($camaras as $camara) {
            fputcsv($fileDestino, $camara);
        }

        fclose($fileDestino);

        $this->info("\n✅ ¡Importación completada!");
        $this->info("   Total de cámaras importadas: " . count($camaras));
        $this->info("   Archivo guardado en: $rutaDestino");
        $this->info("\n💡 Ahora puedes ejecutar:");
        $this->info("   php artisan camaras:escanear    # Para verificar el estado de las cámaras");
        $this->info("   php artisan camaras:sincronizar-fly  # Para sincronizar con Fly.io (si es necesario)");

        return 0;
    }
}
