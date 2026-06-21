<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{


    /**
     * Ruta al archivo de base de datos SQLite activo.
     */
    private function dbPath(): string
    {
        $path = config('database.connections.sqlite.database');
        // Handle in-memory or default edge cases
        if ($path === ':memory:' || !$path) {
            return database_path('database.sqlite');
        }
        return $path;
    }

    /**
     * Directorio donde se guardan los backups.
     */
    private function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    // ─────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────
    public function index()
    {
        $files = File::files($this->backupDir());

        $backups = [];
        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['sqlite', 'db'])) {
                $backups[] = [
                    'name'      => $file->getFilename(),
                    'size'      => $this->formatSizeUnits($file->getSize()),
                    'date'      => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Ordenar por fecha descendente
        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return view('backups.index', compact('backups'));
    }

    // ─────────────────────────────────────────────
    //  CREATE  — copia simple del .sqlite
    // ─────────────────────────────────────────────
    public function create()
    {
        $source = $this->dbPath();

        if (!File::exists($source)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el archivo de base de datos SQLite.',
            ], 500);
        }

        $filename   = 'backup_' . date('d-m-Y_H\hi') . '.sqlite';
        $backupPath = $this->backupDir() . DIRECTORY_SEPARATOR . $filename;

        try {
            File::copy($source, $backupPath);

            $fileInfo = new \SplFileInfo($backupPath);
            $newBackup = [
                'name'      => $fileInfo->getFilename(),
                'size'      => $this->formatSizeUnits($fileInfo->getSize()),
                'date'      => Carbon::createFromTimestamp($fileInfo->getMTime())->format('Y-m-d H:i:s'),
                'timestamp' => $fileInfo->getMTime(),
            ];

            return response()->json([
                'success' => true,
                'message' => '✅ Backup creado exitosamente',
                'backup'  => $newBackup,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    //  DOWNLOAD
    // ─────────────────────────────────────────────
    public function download($name)
    {
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $name;

        if (!File::exists($path)) {
            abort(404, 'El archivo de backup no existe.');
        }

        // MIME explícito para evitar depender de php_fileinfo
        return response()->download($path, $name, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
        ]);
    }

    // ─────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────
    public function destroy($name)
    {
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $name;

        if (!File::exists($path)) {
            return response()->json(['success' => false, 'message' => 'El archivo no existe.'], 404);
        }

        File::delete($path);
        return response()->json(['success' => true, 'message' => '✅ Backup eliminado exitosamente']);
    }

    // ─────────────────────────────────────────────
    //  RESTORE (desde backup del servidor)
    // ─────────────────────────────────────────────
    public function restore(\App\Http\Requests\RestoreBackupRequest $request, $name)
    {

        $backupPath = $this->backupDir() . DIRECTORY_SEPARATOR . $name;

        if (!File::exists($backupPath)) {
            return response()->json(['success' => false, 'message' => 'El archivo de backup no existe.'], 404);
        }

        return $this->performRestore($backupPath);
    }

    // ─────────────────────────────────────────────
    //  RESTORE UPLOAD (desde archivo externo)
    // ─────────────────────────────────────────────
    public function restoreUpload(\App\Http\Requests\RestoreBackupRequest $request)
    {
        $file = $request->file('backup_file');
        $ext  = strtolower($file->getClientOriginalExtension());

        // Guardar el archivo subido temporalmente en backups/
        $uploadedName = 'uploaded_' . date('d-m-Y_H\hi') . '.' . $ext;
        $uploadedPath = $this->backupDir() . DIRECTORY_SEPARATOR . $uploadedName;
        $file->move($this->backupDir(), $uploadedName);

        return $this->performRestore($uploadedPath);
    }

    // ─────────────────────────────────────────────
    //  LÓGICA COMÚN DE RESTAURACIÓN
    // ─────────────────────────────────────────────
    private function performRestore(string $sourcePath)
    {
        $destination = $this->dbPath();

        try {
            // Respaldo automático del estado actual antes de restaurar
            $safetyPath = $this->backupDir() . DIRECTORY_SEPARATOR
                        . 'pre_restore_' . date('d-m-Y_H\hi') . '.sqlite';

            if (File::exists($destination)) {
                File::copy($destination, $safetyPath);
            }

            // Restaurar: sobreescribir la base de datos activa con el backup
            File::copy($sourcePath, $destination);

            return response()->json([
                'success' => true,
                'message' => '✅ Base de datos restaurada exitosamente. Se guardó un respaldo automático del estado anterior.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────
    private function formatSizeUnits(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576,    2) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024,       2) . ' KB';
        if ($bytes > 1)           return $bytes . ' bytes';
        if ($bytes == 1)          return '1 byte';
        return '0 bytes';
    }
}
