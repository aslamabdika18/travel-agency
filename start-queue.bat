@echo off
echo Starting Laravel Queue Worker...
cd /d "c:\laragon\www\travel-agency"
php artisan queue:work --verbose --tries=3 --timeout=90
pause