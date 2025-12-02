<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email dari Laravel - Payment Sekolah', function($message) {
        $message->to('keuanganyesa@gmail.com')
                ->subject('Test Email - Payment Sekolah');
    });
    
    echo "✓ Email berhasil dikirim ke keuanganyesa@gmail.com\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
