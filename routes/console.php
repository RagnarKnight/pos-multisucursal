<?php

use Illuminate\Support\Facades\Schedule;

// ── Backup completo: DB + imágenes ─────────────────────────────
// Mediodía: durante el turno, por si algo pasa
Schedule::command('backup:full')->dailyAt('12:00');

// Cierre del día: el más importante
Schedule::command('backup:full')->dailyAt('23:00');

// ── Comandos manuales disponibles ─────────────────────────────
// Solo DB:      php artisan backup:full --solo-db
// Solo imágenes: php artisan backup:full --solo-imgs
// Completo:     php artisan backup:full
