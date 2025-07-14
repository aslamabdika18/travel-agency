<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project

This is a **Travel Booking System** built with Laravel, featuring:

- **Travel Package Management** - Browse and book travel packages
- **User Authentication & Authorization** - Role-based access control
- **Payment Integration** - Midtrans payment gateway integration
- **Admin Dashboard** - Filament-based admin panel
- **Responsive Design** - Modern UI with Tailwind CSS
- **Booking Management** - Complete booking lifecycle management

### Key Features

- 🏝️ **Travel Packages** - Detailed package information with galleries
- 💳 **Secure Payments** - Midtrans Snap integration for secure transactions
- 👥 **User Management** - Customer and admin role management
- 📊 **Admin Dashboard** - Comprehensive admin panel with Filament
- 📱 **Responsive Design** - Mobile-friendly interface
- 🔒 **Security** - CSRF protection, input validation, and secure authentication

## Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- SQLite (default) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd backend
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate --seed
```

5. **Build assets**
```bash
npm run build
```

6. **Start development server**
```bash
php artisan serve
```

### Midtrans Payment Gateway Setup

1. **Get Midtrans credentials** from [Midtrans Dashboard](https://dashboard.midtrans.com/)

2. **Configure environment variables** in `.env`:
```env
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false  # Set to true for production
```

3. **Test configuration**:
```bash
php scripts/test-midtrans-config.php local
```

### Production Deployment

For production deployment, please refer to:
- [Midtrans Production Deployment Guide](docs/midtrans-production-deployment.md)
- [Environment Setup Guide](ENVIRONMENT_SETUP.md)

**Important**: SSL verification is automatically enabled in production for security.

## Project Structure

```
backend/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Filament/            # Admin panel resources
├── config/
│   ├── midtrans.php         # Midtrans configuration
│   └── ...
├── docs/                    # Project documentation
│   ├── midtrans-integration.md
│   ├── midtrans-production-deployment.md
│   └── ...
├── resources/
│   ├── views/               # Blade templates
│   ├── js/                  # Frontend JavaScript
│   └── css/                 # Stylesheets
├── scripts/                 # Utility scripts
│   ├── test-midtrans-config.php
│   └── ...
└── routes/                  # Application routes
```

## Available Documentation

- [Midtrans Integration Guide](docs/midtrans-integration.md)
- [Production Deployment Guide](docs/midtrans-production-deployment.md)
- [Laragon SSL Configuration Guide](docs/laragon-ssl-configuration.md)
- [Filament Admin Integration](docs/filament-integration.md)
- [RBAC Integration](docs/rbac-integration.md)
- [Session Troubleshooting](docs/session-troubleshooting.md)

## Testing

### Run Tests
```bash
php artisan test
```

### Test Midtrans Configuration
```bash
# Test local environment
php scripts/test-midtrans-config.php local

# Test staging environment
php scripts/test-midtrans-config.php staging

# Test production environment
php scripts/test-midtrans-config.php production
```

### SSL Configuration Testing
```bash
# Test SSL configuration (after setup)
php D:\laragon\www\test-ssl.php

# Or visit in browser
http://localhost/test-ssl.php
```

## Laragon SSL Setup (Windows Development)

If you're using Laragon and encountering SSL certificate errors when connecting to external APIs (like Midtrans), follow these steps:

### Automatic Setup (Recommended)

**Option 1: Using Batch Script**
```cmd
# Run as Administrator
scripts\setup-laragon-ssl.bat
```

**Option 2: Using PowerShell Script**
```powershell
# Run PowerShell as Administrator
.\scripts\setup-laragon-ssl.ps1

# With custom Laragon path
.\scripts\setup-laragon-ssl.ps1 -LaragonPath "C:\laragon"

# Skip Composer configuration
.\scripts\setup-laragon-ssl.ps1 -SkipComposer
```

### Manual Setup
See detailed instructions in [Laragon SSL Configuration Guide](docs/laragon-ssl-configuration.md)

### After Setup
1. Restart Laragon
2. Restart your terminal
3. Test SSL configuration:
   ```bash
   php D:\laragon\www\test-ssl.php
   ```

## Security Considerations

### Development Environment
- SSL verification is disabled for Midtrans API calls
- Debug mode is enabled
- Detailed error reporting
- Laragon SSL certificates configured

### Production Environment
- SSL verification is automatically enabled
- Debug mode is disabled
- Error logging without sensitive data exposure
- CSRF protection enabled
- Input validation and sanitization

### Environment-Specific SSL Configuration

The application automatically configures SSL settings based on the environment:

```php
// Development: SSL verification disabled
if (config('app.env') === 'local' || config('app.env') === 'development') {
    Config::$curlOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];
}

// Production: SSL verification enabled
else {
    Config::$curlOptions = [
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO => env('MIDTRANS_SSL_CERT_PATH', null),
    ];
}
```

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
=======
# travel-agency

## License
