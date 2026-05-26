# Copilot Instructions - Sekolah Management System

## Project Overview
This is a Laravel 10 school management system running on XAMPP (PHP 8.1+) for managing students (siswa), school finances (kas sekolah), student billing (tagihan), and classes (kelas/jurusan). Built with InfyOm Laravel Generator for CRUD scaffolding, AdminLTE UI, and Spatie Permissions for RBAC.

## Architecture Patterns

### Repository Pattern
All models have corresponding repositories in `app/Repositories/` extending `BaseRepository`:
- Use repositories for data access (e.g., `$this->siswaRepository->all()`)
- Repositories inject into controllers via constructor DI
- Example: `SiswaRepository`, `KasSekolahRepository`, `TagihanRepository`

### Role-Based Access Control (RBAC)
- **Roles**: `super-admin`, `admin`, `student`
- **Middleware**: `CheckRole` at `app/Http/Middleware/CheckRole.php`
- Usage: `->middleware('role:admin|super-admin')` on routes/controller methods
- Hybrid system: legacy `users.role` column + Spatie Permission package
- Seeders: `SuperAdminSeeder` (env: `SUPERADMIN_EMAIL`, `SUPERADMIN_PASSWORD`)

### Activity Logging
- Service: `App\Services\ActivityLogger::log()` for manual logging
- Observer: `ActivityObserver` auto-logs `created`, `updated`, `deleted` events for admin/super-admin actions only
- Model: `ActivityLog` stores JSON `old_values`/`new_values`
- Only admin and super-admin actions are logged

### Data Models
- **Siswa** (`tb_siswa`): Students with progress tracking via `progress` attribute, soft deletes enabled
- **KasSekolah** (`tb_kas_sekolah`): School cash flow (`tipe=1` for income, `tipe=2` for expenses)
- **Tagihan** (`tb_tagihan_siswa`): Student billing records
- **Kelas** (`tb_kelas`): Class/jurusan mapping with `kode` field
- Relationships: `Siswa->jurusans()` belongsTo `Kelas`

## Development Workflows

### Setup Commands
```bash
composer install                    # PHP dependencies
npm install && npm run dev          # Frontend assets (Vite + AdminLTE)
php artisan migrate                 # Run migrations
php artisan db:seed                 # Seed super-admin (check SUPERADMIN_EMAIL in .env)
```

### Testing
- **Separate test database**: `db_sekolah_testing` (see `TESTING.md`)
- Setup once: `php artisan migrate --env=testing --force`
- Run tests: `php artisan test` or `vendor/bin/phpunit`

### Key Artisan Commands
```bash
php artisan migrate                           # Run migrations
php artisan db:seed --class=SuperAdminSeeder # Create super-admin
php artisan route:list                        # View all routes
php artisan make:model ModelName              # Generate model
php artisan optimize:clear                    # Clear all caches
```

### Generator Usage (InfyOm)
Access generator UI at `/generator_builder` when logged in. Generates full CRUD:
- Model, Repository, Controller, Request, Views
- Config: `config/laravel_generator.php` and `config/infyom/`
- Schemas: `resources/model_schemas/`

## Project-Specific Conventions

### Database Naming
- Tables: `tb_` prefix (e.g., `tb_siswa`, `tb_kas_sekolah`)
- Use snake_case for columns
- Timestamps and soft deletes are standard

### Controller Patterns
- Extend `AppBaseController` for shared flash message handling
- Use Form Requests for validation (`CreateSiswaRequest`, `UpdateSiswaRequest`)
- Role checks via middleware in constructor:
  ```php
  $this->middleware('role:admin|super-admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
  ```

### View Patterns
- AdminLTE templates in `resources/views/`
- Flash messages via `laracasts/flash` package
- DataTables integration: Yajra DataTables package for listing pages

### Date Range Filtering
HomeController uses custom `resolveRange()` method for filtering by `today|week|month`. Returns `[$start, $end, $label]` Carbon instances.

### Excel Export
- Exports in `app/Exports/` using Maatwebsite Excel
- Examples: `KasRangeExport`, `LowProgressSiswaExport`, `SiswaExport`
- Implement `FromCollection`, `WithHeadings`, `WithStyles` interfaces

### Import Pattern
- `SiswaImport` at `app/Imports/` for bulk student import
- Template generation: `AdminController@generateTemplate` creates Excel template from `Kelas` data
- Route: `/admin/generate-template` downloads template

## Key Routes & Features

### Public Routes
- `/` - Home dashboard with kas overview and student progress charts
- `/cek-tagihan` - Public billing lookup
- `/cek-kas` - Public cash flow report

### Admin Routes (`role:admin|super-admin`)
- `/admin` - Admin panel for student promotions/graduations
- `/admin/kenaikan-kelulusan` - POST: Bulk promote students (10→11→12→graduate)
- `/admin/import-siswa` - POST: Import students from Excel

### Super-Admin Routes (`role:super-admin`)
- `/users` - User management resource
- `/activity-logs` - View activity audit logs

### Resource Routes
Standard CRUD for: `kas-sekolahs`, `siswas`, `kelas`, `tagihans`

## Integration Points

### External Dependencies
- **AdminLTE 3.1.0**: UI framework (via npm)
- **Yajra DataTables**: Server-side tables (`yajra/laravel-datatables-oracle`)
- **Spatie Permission**: RBAC (`spatie/laravel-permission`)
- **Maatwebsite Excel**: Import/export (`maatwebsite/excel`)
- **DomPDF**: PDF generation (`barryvdh/laravel-dompdf`)
- **InfyOm Generator**: CRUD scaffolding (`infyomlabs/laravel-generator`)

### Frontend Build
- Vite for asset compilation (`vite.config.js`)
- Entry: `resources/sass/app.scss`, `resources/js/app.js`
- Public assets: `public/build/` (generated)

## Common Tasks

### Adding New Model with CRUD
1. Use generator UI at `/generator_builder` or artisan commands
2. Ensure repository extends `BaseRepository`
3. Add role middleware to controller constructor if needed
4. Register observer in `AppServiceProvider` if activity logging required

### Adding Activity Logging to Model
```php
// In AppServiceProvider::boot()
ModelName::observe(ActivityObserver::class);
```

### Creating Excel Export
Implement `FromCollection` + `WithHeadings` + `WithStyles` in `app/Exports/`, use `Excel::download()` in controller.

### Updating Permissions
Check `routes/web.php` middleware and `CheckRole` implementation. Ensure Spatie roles synced with `users.role` column.
