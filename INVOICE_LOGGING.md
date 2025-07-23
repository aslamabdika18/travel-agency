# Invoice Logging System Documentation

## Overview

Sistem logging invoice yang komprehensif untuk monitoring, error handling, dan performance tracking pada fitur PDF invoice dalam aplikasi travel agency.

## Features

### 1. Comprehensive Logging
- **Generation Logging**: Track semua proses pembuatan PDF invoice
- **Download Logging**: Monitor aktivitas download invoice
- **Email Attachment Logging**: Track proses attachment PDF ke email
- **Error Logging**: Detailed error tracking dengan stack trace
- **Performance Logging**: Monitor slow requests dan performance issues
- **System Events**: Log maintenance dan system activities

### 2. Multiple Log Channels
- `invoice`: General invoice activities
- `invoice_errors`: Error-specific logs
- `invoice_performance`: Performance monitoring

### 3. Middleware Integration
- `InvoiceActivityLogger`: Middleware untuk track semua request ke invoice endpoints
- Automatic request/response logging
- Performance monitoring
- Security event detection

### 4. Maintenance Commands
- `php artisan invoice:maintenance --check`: Check service configuration
- `php artisan invoice:maintenance --stats`: Show statistics
- `php artisan invoice:maintenance --cleanup --days=30`: Cleanup old files

## File Structure

```
app/
├── Helpers/
│   └── InvoiceLogger.php          # Helper class untuk structured logging
├── Http/
│   ├── Controllers/
│   │   └── PageController.php      # Updated dengan logging
│   └── Middleware/
│       └── InvoiceActivityLogger.php # Middleware untuk request logging
├── Notifications/
│   └── PaymentSuccessNotification.php # Updated dengan logging
├── Services/
│   └── InvoiceService.php          # Updated dengan logging
└── Console/
    └── Commands/
        └── InvoiceMaintenanceCommand.php # Command untuk maintenance

config/
├── invoice.php                     # Konfigurasi invoice system
└── logging.php                     # Updated dengan invoice channels

routes/
├── console.php                     # Updated dengan invoice commands
└── web.php                         # Updated dengan logging middleware

storage/
└── logs/
    ├── invoice-YYYY-MM-DD.log      # General invoice logs
    ├── invoice-errors-YYYY-MM-DD.log # Error logs
    └── invoice-performance-YYYY-MM-DD.log # Performance logs
```

## Configuration

### Environment Variables

Tambahkan ke file `.env`:

```env
# Invoice System Configuration
INVOICE_LOGGING_ENABLED=true
INVOICE_LOG_CHANNEL=invoice
INVOICE_LOG_LEVEL=info
INVOICE_LOG_DAYS=30
INVOICE_ERROR_LOG_DAYS=60
INVOICE_PERFORMANCE_LOG_DAYS=14
INVOICE_LOG_REQUESTS=true
INVOICE_LOG_GENERATION=true
INVOICE_LOG_DOWNLOADS=true
INVOICE_LOG_ERRORS=true
INVOICE_LOG_PERFORMANCE=true
INVOICE_SLOW_REQUEST_THRESHOLD=5000

# Invoice Security Configuration
INVOICE_RATE_LIMIT_ENABLED=true
INVOICE_RATE_LIMIT_ATTEMPTS=10
INVOICE_RATE_LIMIT_DECAY=60

# Invoice Email Configuration
INVOICE_EMAIL_ATTACH_PDF=true
INVOICE_EMAIL_MAX_SIZE=5242880
INVOICE_EMAIL_RETRY_ATTEMPTS=3

# Invoice Performance Configuration
INVOICE_CACHE_ENABLED=true
INVOICE_CACHE_TTL=3600
INVOICE_MAX_GENERATION_TIME=30
INVOICE_MEMORY_LIMIT=256M

# Invoice Maintenance Configuration
INVOICE_AUTO_CLEANUP=true
INVOICE_CLEANUP_SCHEDULE=weekly
INVOICE_KEEP_SUCCESSFUL_DAYS=30
INVOICE_KEEP_FAILED_DAYS=7
INVOICE_MAX_FILES_PER_CLEANUP=100
INVOICE_BACKUP_BEFORE_CLEANUP=false

# Invoice Monitoring Configuration
INVOICE_MONITORING_ENABLED=true
INVOICE_ALERT_ON_ERRORS=true
INVOICE_ALERT_ON_SLOW_REQUESTS=true
INVOICE_HEALTH_CHECK_INTERVAL=300
```

## Usage

### 1. Automatic Logging

Logging akan berjalan otomatis untuk:
- Semua request ke route `/payment/invoice/{payment}`
- Proses generation PDF invoice
- Download invoice
- Email attachment
- Error handling

### 2. Manual Logging

```php
use App\Helpers\InvoiceLogger;

// Log custom events
InvoiceLogger::logSystemEvent('custom_event', [
    'data' => 'value'
]);

// Log maintenance activities
InvoiceLogger::logMaintenance('cleanup', [
    'files_deleted' => 10
]);
```

### 3. Monitoring Commands

```bash
# Check service configuration
php artisan invoice:check

# Show statistics
php artisan invoice:stats

# Cleanup old files (7 days)
php artisan invoice:cleanup 7

# Full maintenance check
php artisan invoice:maintenance --check --stats

# Cleanup with custom days
php artisan invoice:maintenance --cleanup --days=30
```

## Log Format

### Invoice Generation Log
```json
{
    "event": "invoice_generation_success",
    "payment_id": 123,
    "booking_id": 456,
    "booking_reference": "TRV-2024-001",
    "user_id": 789,
    "file_path": "/path/to/invoice.pdf",
    "file_size_bytes": 245760,
    "file_size_mb": 0.23,
    "generation_time_ms": 1250.45,
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

### Download Log
```json
{
    "event": "invoice_download_success",
    "payment_id": 123,
    "user_id": 789,
    "ip_address": "192.168.1.100",
    "file_size_mb": 0.23,
    "download_time_ms": 850.25,
    "timestamp": "2024-01-15T10:35:00.000Z"
}
```

### Error Log
```json
{
    "event": "invoice_generation_error",
    "payment_id": 123,
    "error_message": "PDF generation failed",
    "error_file": "/app/Services/InvoiceService.php",
    "error_line": 125,
    "stack_trace": "...",
    "timestamp": "2024-01-15T10:40:00.000Z"
}
```

## Monitoring & Alerts

### 1. Performance Monitoring
- Automatic detection untuk slow requests (>5 detik default)
- Memory usage tracking
- File size monitoring

### 2. Error Detection
- Comprehensive error logging dengan stack trace
- Automatic categorization (generation, download, email)
- Failed request tracking

### 3. Security Monitoring
- Unauthorized access attempts
- Rate limiting violations
- Suspicious activity detection

## Maintenance

### 1. Automatic Cleanup
- Scheduled cleanup setiap minggu (Sunday 4 AM)
- Configurable retention period
- Safe deletion dengan backup option

### 2. Log Rotation
- Daily log files dengan automatic rotation
- Configurable retention period per log type
- Automatic compression untuk old logs

### 3. Health Checks
- Service configuration validation
- Storage space monitoring
- Performance metrics collection

## Troubleshooting

### 1. Common Issues

**Log files tidak terbuat:**
- Check permission folder `storage/logs`
- Verify `INVOICE_LOGGING_ENABLED=true`
- Check log channel configuration

**Performance issues:**
- Monitor `invoice-performance.log`
- Check `INVOICE_SLOW_REQUEST_THRESHOLD` setting
- Review memory usage logs

**Missing logs:**
- Verify middleware registration
- Check route configuration
- Validate environment variables

### 2. Debug Mode

```env
# Enable debug logging
INVOICE_LOG_LEVEL=debug
LOG_LEVEL=debug
```

### 3. Log Analysis

```bash
# View recent invoice logs
tail -f storage/logs/invoice-$(date +%Y-%m-%d).log

# Search for errors
grep "error" storage/logs/invoice-errors-*.log

# Monitor performance
grep "slow" storage/logs/invoice-performance-*.log
```

## Best Practices

1. **Regular Monitoring**: Check logs secara berkala untuk detect issues
2. **Performance Tuning**: Monitor slow requests dan optimize accordingly
3. **Storage Management**: Setup automatic cleanup untuk prevent disk space issues
4. **Security Review**: Regular review untuk unauthorized access attempts
5. **Backup Strategy**: Backup important logs sebelum cleanup

## Integration dengan Monitoring Tools

Sistem logging ini dapat diintegrasikan dengan:
- **ELK Stack** (Elasticsearch, Logstash, Kibana)
- **Grafana** untuk visualization
- **Slack/Discord** untuk alerts
- **Email notifications** untuk critical errors

## Support

Untuk pertanyaan atau issues terkait invoice logging system:
1. Check log files untuk error details
2. Run diagnostic commands
3. Review configuration settings
4. Contact development team dengan log excerpts