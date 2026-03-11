<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:full
                                {--solo-db   : Solo la base de datos, sin imágenes}
                                {--solo-imgs : Solo las imágenes, sin base de datos}';
    protected $description = 'Backup completo: base de datos SQLite + imágenes (logos, comprobantes, productos)';

    // Máximo de backups a conservar en disco
    const MAX_BACKUPS = 30;

    public function handle(): int
    {
        $soloDb   = $this->option('solo-db');
        $soloImgs = $this->option('solo-imgs');
        $fecha    = now()->format('Y-m-d_H-i');

        // ── Carpeta de destino ────────────────────────────────────
        $carpeta = storage_path('app/backups');
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        $archivosCreados = [];

        // ── 1. Backup de la base de datos ─────────────────────────
        if (!$soloImgs) {
            $resultado = $this->backupDB($carpeta, $fecha);
            if ($resultado === false) return self::FAILURE;
            $archivosCreados[] = $resultado;
        }

        // ── 2. Backup de imágenes ─────────────────────────────────
        if (!$soloDb) {
            $resultado = $this->backupImagenes($carpeta, $fecha);
            if ($resultado === false) return self::FAILURE;
            if ($resultado !== null) $archivosCreados[] = $resultado;
        }

        // ── 3. Limpiar backups viejos ─────────────────────────────
        $this->limpiarViejos($carpeta);

        // ── Resumen ───────────────────────────────────────────────
        $this->info('');
        $this->info('✅ Backup completado — ' . now()->format('d/m/Y H:i'));
        foreach ($archivosCreados as $archivo) {
            $kb = number_format(filesize($archivo) / 1024, 1);
            $this->line('   📦 ' . basename($archivo) . " ({$kb} KB)");
        }
        $totalBackups = count(glob("{$carpeta}/backup_*.sqlite"))
                      + count(glob("{$carpeta}/imagenes_*.zip"));
        $this->line("   📂 Backups en disco: {$totalBackups}");

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────
    private function backupDB(string $carpeta, string $fecha): string|false
    {
        $origen  = database_path('database.sqlite');
        $destino = "{$carpeta}/backup_{$fecha}.sqlite";

        if (!file_exists($origen)) {
            $this->error('❌ No se encontró database.sqlite');
            return false;
        }

        // WAL checkpoint: vuelca el WAL a la DB principal antes de copiar
        // Evita backups inconsistentes en SQLite con WAL mode activo
        try {
            $pdo = new \PDO('sqlite:' . $origen);
            $pdo->exec('PRAGMA wal_checkpoint(TRUNCATE)');
        } catch (\Exception $e) {
            $this->warn('⚠️  WAL checkpoint falló, continuando igual...');
        }

        if (!copy($origen, $destino)) {
            $this->error('❌ Error al copiar la base de datos.');
            return false;
        }

        return $destino;
    }

    // ─────────────────────────────────────────────────────────────
    private function backupImagenes(string $carpeta, string $fecha): string|false|null
    {
        // Carpetas a incluir dentro de storage/app/public/
        $subcarpetas = ['comprobantes', 'logos', 'productos'];
        $publicBase  = storage_path('app/public');

        // Verificar si hay algo para respaldar
        $hayArchivos = false;
        foreach ($subcarpetas as $sub) {
            $dir = "{$publicBase}/{$sub}";
            if (is_dir($dir) && count(glob("{$dir}/*")) > 0) {
                $hayArchivos = true;
                break;
            }
        }

        if (!$hayArchivos) {
            $this->line('   ℹ️  Sin imágenes para respaldar todavía.');
            return null;
        }

        if (!class_exists('ZipArchive')) {
            $this->warn('⚠️  ZipArchive no disponible. Instalá php-zip para backup de imágenes.');
            return null;
        }

        $destino = "{$carpeta}/imagenes_{$fecha}.zip";
        $zip     = new ZipArchive();

        if ($zip->open($destino, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('❌ No se pudo crear el ZIP de imágenes.');
            return false;
        }

        $totalArchivos = 0;
        foreach ($subcarpetas as $sub) {
            $dir = "{$publicBase}/{$sub}";
            if (!is_dir($dir)) continue;

            $archivos = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($archivos as $archivo) {
                if ($archivo->isFile()) {
                    // Ruta dentro del ZIP: comprobantes/foto.jpg
                    $rutaEnZip = $sub . '/' . $archivo->getFilename();
                    $zip->addFile($archivo->getPathname(), $rutaEnZip);
                    $totalArchivos++;
                }
            }
        }

        $zip->close();

        if ($totalArchivos === 0) {
            unlink($destino);
            return null;
        }

        $this->line("   🖼️  {$totalArchivos} imagen(es) incluida(s) en el ZIP");
        return $destino;
    }

    // ─────────────────────────────────────────────────────────────
    private function limpiarViejos(string $carpeta): void
    {
        // Limpiar DBs viejas
        $dbs = glob("{$carpeta}/backup_*.sqlite") ?: [];
        if (count($dbs) > self::MAX_BACKUPS) {
            usort($dbs, fn($a, $b) => filemtime($a) - filemtime($b));
            foreach (array_slice($dbs, 0, count($dbs) - self::MAX_BACKUPS) as $viejo) {
                unlink($viejo);
            }
        }

        // Limpiar ZIPs viejos
        $zips = glob("{$carpeta}/imagenes_*.zip") ?: [];
        if (count($zips) > self::MAX_BACKUPS) {
            usort($zips, fn($a, $b) => filemtime($a) - filemtime($b));
            foreach (array_slice($zips, 0, count($zips) - self::MAX_BACKUPS) as $viejo) {
                unlink($viejo);
            }
        }
    }
}
