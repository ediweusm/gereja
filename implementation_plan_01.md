# Setup Laravel 11 + Filament v3 + Filament Shield in `sig`

Set up a modern, high-performance local development environment using Laravel 11, Filament v3, MySQL 8, and Filament Shield for role-based access control (RBAC).

## Proposed Changes

We will construct the environment in the empty directory [sig](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig).

### Laravel 11 Base Setup
1. **Initialize Project**: Run `composer create-project laravel/laravel .` inside the `sig` folder to bootstrap Laravel 11.
2. **Environment Configuration**: Set up [.env](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/.env) with MySQL database credentials:
   - Database name: `sig`
   - Host: `172.17.26.159`
   - Username: `root`
   - Password: `root`
3. **Database Migration**: Run `php artisan migrate` to create default tables.

### Filament v3 Installation
1. **Require Filament package**:
   ```bash
   composer require filament/filament:"^3.2" -W
   ```
2. **Install Filament Panels**:
   ```bash
   php artisan filament:install --panels
   ```
   This command installs the default `admin` panel and creates the panel provider [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php).

### Filament Shield (RBAC) Installation
1. **Require Filament Shield**:
   ```bash
   composer require bezhansalleh/filament-shield
   ```
2. **Publish Configuration and Assets**:
   ```bash
   php artisan vendor:publish --tag="filament-shield-config"
   ```
3. **Modify User Model**:
   Update [User.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/User.php) to use `Spatie\Permission\Traits\HasRoles`.
4. **Register Plugin**:
   Add `BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()` to [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php).
5. **Run Shield Installation**:
   ```bash
   php artisan shield:install
   ```
   We will select default options during setup.
6. **Generate Super Admin**:
   ```bash
   php artisan shield:super-admin
   ```
   This will prompt us to create a Super Admin user. We will configure it as:
   - Name: `Super Admin`
   - Email: `admin@sig.test`
   - Password: `password`

## Verification Plan

### Automated Verification
- Run `php artisan db:seed` or check database tables using `mysql -u root -proot -e "USE sig; SHOW TABLES;"` to ensure Spatie permissions and role tables are created.
- Access the shell command status to verify there are no syntax or configuration errors.

### Manual Verification
- We will boot the PHP development server (`php artisan serve`) and use the browser agent to navigate to `http://127.0.0.1:8000/admin/login` and log in successfully with the Super Admin credentials.
