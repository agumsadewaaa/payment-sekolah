# Security Checklist untuk Deployment Production

## 1. Environment Variables (.env)

### Critical Settings - WAJIB DIUBAH sebelum production:

```bash
# Application
APP_ENV=production
APP_DEBUG=false  # PENTING: Set false di production!
APP_KEY=base64:...  # Generate dengan: php artisan key:generate

# Database - Gunakan credentials yang aman
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sekolah
DB_USERNAME=db_user  # JANGAN gunakan root!
DB_PASSWORD=strong_password_here  # Password yang kuat!

# Super Admin - Ubah credentials default
SUPERADMIN_EMAIL=admin@yourdomain.com  # Email valid
SUPERADMIN_PASSWORD=StrongP@ssw0rd123!  # Password kuat (min 12 char)

# Session & Cache
SESSION_DRIVER=database  # atau redis untuk production
SESSION_LIFETIME=120
CACHE_DRIVER=redis  # Lebih cepat dari file

# Queue
QUEUE_CONNECTION=redis  # Untuk background jobs

# Mail - Setup SMTP production
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 2. File Permissions (Linux/Ubuntu Server)

```bash
# Set ownership
sudo chown -R www-data:www-data /path/to/sekolah

# Storage dan cache harus writable
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 app database resources routes

# Protect .env file
sudo chmod 600 .env
```

## 3. Security Headers (Nginx/Apache)

### Nginx Config
```nginx
# /etc/nginx/sites-available/sekolah

server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/sekolah/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:" always;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 4. Database Security

### Buat User Database Terpisah
```sql
-- Login sebagai root
mysql -u root -p

-- Buat database dan user khusus
CREATE DATABASE db_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sekolah_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';

-- Grant privileges (hanya yang diperlukan)
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER 
ON db_sekolah.* TO 'sekolah_user'@'localhost';

FLUSH PRIVILEGES;
```

### Backup Database Rutin
```bash
# Cron job untuk backup harian (tambah ke crontab)
0 2 * * * /usr/bin/mysqldump -u backup_user -p'password' db_sekolah | gzip > /backup/db_sekolah_$(date +\%Y\%m\%d).sql.gz

# Simpan 30 hari terakhir
0 3 * * * find /backup -name "db_sekolah_*.sql.gz" -mtime +30 -delete
```

## 5. Laravel Optimizations untuk Production

```bash
# Clear semua cache
php artisan optimize:clear

# Generate optimized autoload
composer install --optimize-autoloader --no-dev

# Cache configs dan routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan migrations
php artisan migrate --force

# Seed super admin
php artisan db:seed --class=SuperAdminSeeder --force
```

## 6. SSL/TLS Certificate (HTTPS)

### Let's Encrypt (Gratis)
```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Dapatkan certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (sudah otomatis via cron)
sudo certbot renew --dry-run
```

## 7. Firewall (UFW)

```bash
# Enable firewall
sudo ufw enable

# Allow SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Deny MySQL dari luar (hanya localhost)
sudo ufw deny 3306/tcp

# Check status
sudo ufw status
```

## 8. Monitoring & Logging

### Setup Log Rotation
```bash
# /etc/logrotate.d/laravel
/var/www/sekolah/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

### Error Reporting
```bash
# .env - Production settings
LOG_CHANNEL=daily
LOG_LEVEL=error  # Only log errors, tidak warning/info

# Untuk debugging (sementara):
LOG_LEVEL=debug
```

## 9. Prevent Common Attacks

### Rate Limiting (sudah built-in Laravel)
Di `routes/web.php`:
```php
Route::middleware(['throttle:60,1'])->group(function () {
    // Public routes dengan rate limit
});
```

### CSRF Protection (sudah otomatis di Laravel)
Pastikan semua form memiliki:
```blade
@csrf
```

### SQL Injection Prevention
- Selalu gunakan Eloquent atau Query Builder
- JANGAN pernah: `DB::raw("... WHERE id = $request->id")`
- SELALU: `DB::raw("... WHERE id = ?", [$request->id])`

## 10. Pre-deployment Checklist

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] Password super admin sudah diubah
- [ ] Database user bukan root
- [ ] SSL certificate terpasang (HTTPS)
- [ ] Firewall aktif
- [ ] Log rotation configured
- [ ] Backup database otomatis berjalan
- [ ] File permissions sudah benar
- [ ] Cache sudah di-generate (`config:cache`, `route:cache`)
- [ ] Composer packages production only (`--no-dev`)
- [ ] Testing di staging environment
- [ ] Security headers configured

## 11. Post-deployment Monitoring

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.1-fpm.log

# Monitor disk space
df -h

# Monitor database connections
mysql -u root -p -e "SHOW PROCESSLIST;"
```

## 12. Maintenance Mode

```bash
# Enable maintenance mode (untuk update)
php artisan down --secret="my-secret-token"

# Access site dengan token
https://yourdomain.com/my-secret-token

# Update code
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan optimize

# Disable maintenance mode
php artisan up
```

## Emergency Contacts

- **Server Admin**: [email/phone]
- **Database Admin**: [email/phone]
- **Laravel Developer**: [email/phone]
- **Hosting Provider Support**: [phone/ticket system]

---

**Last Updated**: 4 Februari 2026
