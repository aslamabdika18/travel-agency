# Environment Setup Guide

## Overview

Sistem ini menyediakan konfigurasi environment yang terpisah untuk development, staging, dan production dengan fitur-fitur berikut:

- ✅ **Environment-specific configurations**
- ✅ **Automated environment switching**
- ✅ **Security configurations per environment**
- ✅ **Performance optimizations**
- ✅ **Rate limiting configurations**
- ✅ **Monitoring and logging setup**
- ✅ **File upload restrictions**
- ✅ **Database configurations**

## File Structure

```
backend/
├── .env.development          # Development environment config
├── .env.staging              # Staging environment config
├── .env.production           # Production environment config
├── config/
│   └── environments.php      # Environment-specific settings
├── app/
│   ├── Console/Commands/
│   │   └── EnvironmentSwitchCommand.php
│   ├── Http/Middleware/
│   │   └── EnvironmentMiddleware.php
│   └── Providers/
│       └── EnvironmentServiceProvider.php
└── scripts/
    └── switch-env.php         # Standalone environment switcher
```

## Environment Configurations

### Development Environment
- **Debug Mode**: Enabled
- **Database**: SQLite (untuk kemudahan development)
- **Cache**: Database/File-based
- **Mail**: Log driver
- **Security**: Relaxed settings
- **Features**: Telescope, Debugbar, Query Detector enabled
- **File Uploads**: 10MB limit, multiple file types

### Staging Environment
- **Debug Mode**: Disabled
- **Database**: MySQL
- **Cache**: Redis
- **Mail**: SMTP (Mailtrap untuk testing)
- **Security**: Moderate settings
- **Features**: Telescope enabled, Debugbar disabled
- **File Uploads**: 5MB limit, restricted file types
- **Monitoring**: Sentry, Slack notifications

### Production Environment
- **Debug Mode**: Disabled
- **Database**: MySQL with read/write splitting
- **Cache**: Redis cluster
- **Mail**: Amazon SES
- **Security**: Strict settings
- **Features**: All debugging tools disabled
- **File Uploads**: 2MB limit, very restricted file types
- **Monitoring**: Sentry, New Relic, Slack notifications
- **Performance**: Full optimization enabled

## Setup Instructions

### 1. Register Service Provider

Tambahkan `EnvironmentServiceProvider` ke `config/app.php`:

```php
'providers' => [
    // Other providers...
    App\Providers\EnvironmentServiceProvider::class,
],
```

### 2. Register Middleware

Tambahkan `EnvironmentMiddleware` ke `app/Http/Kernel.php`:

```php
protected $middleware = [
    // Other middleware...
    \App\Http\Middleware\EnvironmentMiddleware::class,
];
```

### 3. Update Environment Files

Sesuaikan nilai-nilai berikut di setiap file environment:

#### Development (.env.development)
- `APP_KEY`: Generate dengan `php artisan key:generate`
- Database credentials (jika menggunakan MySQL)

#### Staging (.env.staging)
- `APP_KEY`: Generate key yang berbeda
- `APP_URL`: URL staging server
- Database credentials
- Redis credentials
- AWS S3 credentials
- Mailtrap credentials
- Slack webhook URL

#### Production (.env.production)
- `APP_KEY`: Generate key yang unik dan aman
- `APP_URL`: URL production
- Database credentials (termasuk read replica)
- Redis cluster credentials
- AWS credentials (S3, SES, CloudFront)
- Midtrans production keys
- Monitoring service credentials (Sentry, New Relic)
- Slack webhook URL

## Usage

### Method 1: Using Artisan Command (Recommended)

```bash
# Switch to development
php artisan env:switch development

# Switch to staging with backup
php artisan env:switch staging --backup

# Switch to production without optimization
php artisan env:switch production --no-optimize

# Switch without clearing caches
php artisan env:switch development --no-cache
```

### Method 2: Using Standalone Script

```bash
# Switch environments
php scripts/switch-env.php development
php scripts/switch-env.php staging
php scripts/switch-env.php production
```

### Method 3: Manual Switching

```bash
# Copy environment file
cp .env.development .env

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Environment-Specific Features

### Rate Limiting

Setiap environment memiliki rate limiting yang berbeda:

- **Development**: 1000 requests/minute (API), 500 requests/minute (Web)
- **Staging**: 500 requests/minute (API), 300 requests/minute (Web)
- **Production**: 100 requests/minute (API), 200 requests/minute (Web)

### Security Headers

- **Development**: CORS enabled, minimal security headers
- **Staging/Production**: Full security headers (CSP, HSTS, XSS Protection)

### File Upload Limits

- **Development**: 10MB, multiple file types
- **Staging**: 5MB, restricted file types
- **Production**: 2MB, very restricted file types

### Caching Strategy

- **Development**: No caching for easier debugging
- **Staging**: 5-minute cache for testing
- **Production**: Full caching with CDN support

## Monitoring and Logging

### Development
- Local file logging
- Debug level logging
- No external monitoring

### Staging
- Sentry error tracking
- Slack notifications for warnings
- Info level logging

### Production
- Sentry error tracking
- New Relic performance monitoring
- Slack notifications for errors
- Error level logging only

## Database Configuration

### Development
- SQLite untuk kemudahan setup
- No connection pooling
- No read/write splitting

### Staging
- MySQL single instance
- Connection pooling enabled
- No read/write splitting

### Production
- MySQL with read/write splitting
- Connection pooling enabled
- Redis clustering

## Performance Optimizations

### Development
- No optimizations untuk debugging
- OPcache disabled
- No CDN

### Staging
- Basic optimizations
- OPcache enabled
- CDN enabled
- Image optimization

### Production
- Full optimizations
- OPcache enabled
- Redis clustering
- CDN with CloudFront
- Image optimization
- Config/route/view caching

## Security Considerations

### Development
- Debug mode enabled
- Relaxed CORS
- HTTP allowed
- Minimal security headers

### Staging
- Debug mode disabled
- HTTPS enforced
- Moderate security headers
- CSRF protection

### Production
- Debug mode disabled
- HTTPS enforced with HSTS
- Full security headers
- Strict CSRF protection
- Content Security Policy

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   chmod +x scripts/switch-env.php
   ```

2. **Cache Issues**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Database Connection Issues**
   - Periksa credentials di file .env
   - Pastikan database sudah dibuat
   - Jalankan migrations: `php artisan migrate`

4. **Asset Issues**
   ```bash
   npm run build
   php artisan storage:link
   ```

### Environment Verification

Untuk memverifikasi environment yang aktif:

```bash
php artisan env:switch --help
php artisan config:show app.env
```

## Best Practices

1. **Selalu backup .env sebelum switching**
2. **Test aplikasi setelah switching environment**
3. **Update credentials sesuai environment**
4. **Monitor logs setelah deployment**
5. **Gunakan environment-specific API keys**
6. **Jangan commit file .env ke repository**
7. **Gunakan secrets management untuk production**

## Next Steps

1. Setup CI/CD pipeline untuk automated deployment
2. Implement health checks untuk monitoring
3. Setup backup strategies
4. Configure load balancing untuk production
5. Implement blue-green deployment

## Support

Jika mengalami masalah:
1. Periksa log files di `storage/logs/`
2. Jalankan `php artisan config:show` untuk debug konfigurasi
3. Periksa environment variables dengan `php artisan tinker`

---

**Note**: Pastikan untuk mengupdate semua credentials dan API keys sebelum menggunakan di staging atau production environment.
