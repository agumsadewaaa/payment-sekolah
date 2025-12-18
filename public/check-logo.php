<?php
/**
 * Script untuk cek apakah logo ada dan bisa dibaca di hosting
 * Akses: https://domainanda.com/check-logo.php
 */

echo "<h2>Cek Logo File untuk Print Tagihan</h2>";

// Cek berbagai lokasi logo yang mungkin
$paths = [
    'logo.jpg' => __DIR__ . '/logo.jpg',
    'images/logo.jpg' => __DIR__ . '/images/logo.jpg',
    'logo.png' => __DIR__ . '/logo.png',
    'images/logo.png' => __DIR__ . '/images/logo.png',
];

echo "<h3>Informasi Server:</h3>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "Public Path: " . __DIR__ . "<br><br>";

echo "<h3>Hasil Pengecekan:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Lokasi</th><th>Path Lengkap</th><th>Ada?</th><th>Readable?</th><th>Ukuran</th></tr>";

$found = false;
foreach ($paths as $label => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $size = $exists ? filesize($path) : 0;
    
    echo "<tr>";
    echo "<td><strong>$label</strong></td>";
    echo "<td style='font-size:11px;'>$path</td>";
    echo "<td style='color:" . ($exists ? 'green' : 'red') . ";'>" . ($exists ? '✓ Ada' : '✗ Tidak ada') . "</td>";
    echo "<td style='color:" . ($readable ? 'green' : 'red') . ";'>" . ($readable ? '✓ Bisa dibaca' : '✗ Tidak bisa') . "</td>";
    echo "<td>" . ($size > 0 ? number_format($size / 1024, 2) . ' KB' : '-') . "</td>";
    echo "</tr>";
    
    if ($exists && $readable) {
        $found = true;
    }
}

echo "</table>";

if (!$found) {
    echo "<br><div style='background:#ffcccc;padding:15px;border-radius:5px;'>";
    echo "<strong>⚠ TIDAK ADA LOGO YANG DITEMUKAN!</strong><br><br>";
    echo "Solusi:<br>";
    echo "1. Upload file logo.jpg ke folder <strong>public/</strong> di hosting<br>";
    echo "2. Atau upload ke folder <strong>public/images/</strong><br>";
    echo "3. Pastikan nama file tepat: <strong>logo.jpg</strong> atau <strong>logo.png</strong> (huruf kecil)<br>";
    echo "4. Set permission file menjadi 644 (chmod 644 logo.jpg)<br>";
    echo "</div>";
} else {
    echo "<br><div style='background:#ccffcc;padding:15px;border-radius:5px;'>";
    echo "<strong>✓ Logo ditemukan!</strong> Print tagihan seharusnya bisa menampilkan logo.";
    echo "</div>";
    
    // Tampilkan preview logo jika ada
    foreach ($paths as $label => $path) {
        if (file_exists($path) && is_readable($path)) {
            $webPath = '/' . $label;
            echo "<br><h3>Preview: $label</h3>";
            echo "<img src='$webPath' style='max-width:200px;border:1px solid #ccc;'>";
            break;
        }
    }
}

echo "<br><br><div style='background:#e6f2ff;padding:15px;border-radius:5px;'>";
echo "<strong>Cara Upload Logo ke Hosting:</strong><br>";
echo "1. Login ke cPanel<br>";
echo "2. Buka File Manager<br>";
echo "3. Masuk ke folder: <strong>public_html/public/</strong> (atau <strong>htdocs/public/</strong>)<br>";
echo "4. Klik Upload dan pilih file logo.jpg dari komputer<br>";
echo "5. Setelah upload, klik kanan file → Change Permissions → Set ke 644<br>";
echo "6. Refresh halaman ini untuk cek hasilnya<br>";
echo "</div>";

echo "<br><small>Script: check-logo.php | Hapus file ini setelah selesai troubleshooting</small>";
?>
