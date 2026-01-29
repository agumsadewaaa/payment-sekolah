<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Struktur tb_kas_sekolah ===\n";
$kasSekolah = DB::select('DESCRIBE tb_kas_sekolah');
foreach ($kasSekolah as $col) {
    echo "{$col->Field} - {$col->Type} - {$col->Null} - {$col->Key}\n";
}

echo "\n=== Struktur tb_kas_siswa ===\n";
$kasSiswa = DB::select('DESCRIBE tb_kas_siswa');
foreach ($kasSiswa as $col) {
    echo "{$col->Field} - {$col->Type} - {$col->Null} - {$col->Key}\n";
}
