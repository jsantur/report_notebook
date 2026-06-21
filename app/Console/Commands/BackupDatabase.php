<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una copia de seguridad de la base de datos SQLite y limpia las antiguas (mantiene 7 días)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando respaldo de base de datos...');

        $databasePath = database_path('database.sqlite');
        
        if (!File::exists($databasePath)) {
            $this->error('Error: No se encontró el archivo database.sqlite');
            return 1;
        }

        // Crear carpeta de backups si no existe
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Nombre del archivo con fecha y hora
        $filename = 'backup_db_' . Carbon::now()->format('Y_m_d_His') . '.sqlite';
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Realizar la copia
        try {
            File::copy($databasePath, $backupPath);
            $this->info("¡Respaldo creado con éxito!: {$filename}");
        } catch (\Exception $e) {
            $this->error('Error al copiar la base de datos: ' . $e->getMessage());
            return 1;
        }

        // --- Lógica de Limpieza (Pilar 5: Mantener solo 7 días) ---
        $this->info('Limpiando respaldos antiguos...');
        $files = File::files($backupDir);
        
        // Ordenar por fecha de modificación (más antiguos primero)
        usort($files, function ($a, $b) {
            return $a->getMTime() <=> $b->getMTime();
        });

        // Si hay más de 7 archivos, borrar los más antiguos
        if (count($files) > 7) {
            $filesToDelete = array_slice($files, 0, count($files) - 7);
            foreach ($filesToDelete as $file) {
                File::delete($file->getRealPath());
                $this->line("Archivo antiguo eliminado: " . $file->getFilename());
            }
        }

        $this->info('Proceso de respaldo completado.');
        return 0;
    }
}
