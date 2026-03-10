<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature   = 'db:backup';
    protected $description = 'Crea un backup de la base de datos SQLite en storage/app/backups/';

    public function handle(): int
    {
        $origen  = database_path('database.sqlite');
        $fecha   = now()->format('Y-m-d_H-i');
        $nombre  = "backup_{$fecha}.sqlite";
        $destino = storage_path("app/backups/{$nombre}");

        // Crear carpeta si no existe
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        if (!file_exists($origen)) {
            $this->error('❌ No se encontró database.sqlite');
            return self::FAILURE;
        }

        // Copiar el archivo
        if (!copy($origen, $destino)) {
            $this->error('❌ Error al copiar la base de datos.');
            return self::FAILURE;
        }

        // Conservar solo los últimos 30 backups (evitar llenar el disco)
        $backups = glob(storage_path('app/backups/backup_*.sqlite'));
        if (count($backups) > 30) {
            usort($backups, fn($a, $b) => filemtime($a) - filemtime($b));
            $sobrantes = array_slice($backups, 0, count($backups) - 30);
            foreach ($sobrantes as $viejo) {
                unlink($viejo);
            }
        }

        $this->info("✅ Backup creado: {$nombre}");
        $this->line("   Tamaño: " . number_format(filesize($destino) / 1024, 1) . " KB");
        $this->line("   Backups guardados: " . count(glob(storage_path('app/backups/backup_*.sqlite'))));

        return self::SUCCESS;
    }
}
