# Testing Setup

## Database Configuration

Aplikasi ini menggunakan **database terpisah untuk testing** agar database utama tidak terpengaruh saat menjalankan test.

### Database yang Digunakan:
- **Database Utama**: `db_sekolah` (untuk development/production)
- **Database Testing**: `db_sekolah_testing` (khusus untuk testing)

## Setup Awal Testing Database

Jalankan perintah berikut **SEKALI SAJA** saat pertama kali setup:

```bash
# 1. Buat database testing (jika belum ada)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS db_sekolah_testing;"

# Atau menggunakan XAMPP MySQL:
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS db_sekolah_testing;"

# 2. Jalankan migration untuk database testing
php artisan migrate --env=testing --force

# 3. Seed data testing (opsional, hanya jika diperlukan)
php artisan db:seed --env=testing --force
```

## Menjalankan Tests

Setelah setup awal, Anda bisa menjalankan test kapan saja tanpa khawatir database utama terhapus:

```bash
php artisan test
```

Test akan otomatis:
- Menggunakan database `db_sekolah_testing`
- Me-reset database testing setiap kali test (karena `RefreshDatabase`)
- **TIDAK** akan menyentuh database utama `db_sekolah`

## Cara Kerja

File `phpunit.xml` sudah dikonfigurasi untuk menggunakan database testing:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="db_sekolah_testing"/>
```

Trait `RefreshDatabase` pada test classes akan:
1. Me-migrate database testing dari awal
2. Menjalankan test
3. Me-rollback setelah test selesai

## Verifikasi

Untuk memastikan database terpisah bekerja dengan baik:

```bash
# Cek jumlah data di kedua database
mysql -u root -e "SELECT 'DB UTAMA:' as info, COUNT(*) as total FROM db_sekolah.users 
UNION ALL SELECT 'DB TESTING:' as info, COUNT(*) as total FROM db_sekolah_testing.users;"
```

## Catatan Penting

- ✅ Database utama (`db_sekolah`) **AMAN** dari test
- ✅ Tidak perlu running seeder setiap kali test
- ✅ Test bisa dijalankan berulang kali tanpa efek samping
- ✅ Database testing akan di-reset otomatis oleh `RefreshDatabase`

## Troubleshooting

### Database testing kosong setelah test
Ini **normal**! Trait `RefreshDatabase` akan me-reset database setiap kali test. Database utama tetap aman.

### Test error "database not found"
Pastikan database testing sudah dibuat:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS db_sekolah_testing;"
```

### Ingin menambah data testing permanen
Edit seeder atau buat factory untuk generate data di dalam test itu sendiri, bukan mengandalkan database yang sudah terisi.
