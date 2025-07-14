@echo off
echo ========================================
echo    Laragon SSL Configuration Setup
echo ========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo Running as Administrator... OK
) else (
    echo ERROR: This script requires Administrator privileges
    echo Please run as Administrator
    pause
    exit /b 1
)

:: Create SSL directory
echo [1/6] Creating SSL directory...
if not exist "D:\laragon\etc\ssl" (
    mkdir "D:\laragon\etc\ssl"
    echo     ✓ Directory created: D:\laragon\etc\ssl
) else (
    echo     ✓ Directory already exists: D:\laragon\etc\ssl
)

:: Download certificate bundle
echo.
echo [2/6] Downloading CA certificate bundle...
echo     Downloading from: https://curl.se/ca/cacert.pem
curl -L -o "D:\laragon\etc\ssl\cacert.pem" "https://curl.se/ca/cacert.pem"
if %errorLevel% == 0 (
    echo     ✓ Certificate downloaded successfully
) else (
    echo     ✗ Failed to download certificate
    echo     Please check your internet connection
    pause
    exit /b 1
)

:: Verify certificate file
echo.
echo [3/6] Verifying certificate file...
if exist "D:\laragon\etc\ssl\cacert.pem" (
    for %%A in ("D:\laragon\etc\ssl\cacert.pem") do set size=%%~zA
    if !size! GTR 100000 (
        echo     ✓ Certificate file verified (Size: !size! bytes)
    ) else (
        echo     ✗ Certificate file seems corrupted (Size: !size! bytes)
        pause
        exit /b 1
    )
) else (
    echo     ✗ Certificate file not found
    pause
    exit /b 1
)

:: Set environment variable
echo.
echo [4/6] Setting environment variable...
setx CURL_CA_BUNDLE "D:\laragon\etc\ssl\cacert.pem" /M >nul 2>&1
if %errorLevel% == 0 (
    echo     ✓ Environment variable CURL_CA_BUNDLE set
) else (
    echo     ✗ Failed to set environment variable
)

:: Configure Composer (if available)
echo.
echo [5/6] Configuring Composer...
where composer >nul 2>&1
if %errorLevel% == 0 (
    composer config --global cafile "D:\laragon\etc\ssl\cacert.pem" >nul 2>&1
    if %errorLevel% == 0 (
        echo     ✓ Composer configured with SSL certificate
    ) else (
        echo     ⚠ Warning: Failed to configure Composer
    )
) else (
    echo     ⚠ Warning: Composer not found in PATH
)

:: Find and update PHP.ini files
echo.
echo [6/6] Updating PHP configuration...
set "php_updated=0"

:: Search for PHP installations in Laragon
for /d %%D in ("D:\laragon\bin\php\php-*") do (
    if exist "%%D\php.ini" (
        echo     Updating: %%D\php.ini
        
        :: Backup original php.ini
        copy "%%D\php.ini" "%%D\php.ini.backup.%date:~-4,4%%date:~-10,2%%date:~-7,2%" >nul 2>&1
        
        :: Check if curl.cainfo already exists
        findstr /C:"curl.cainfo" "%%D\php.ini" >nul 2>&1
        if %errorLevel% == 0 (
            :: Update existing line
            powershell -Command "(Get-Content '%%D\php.ini') -replace '^;?curl\.cainfo.*', 'curl.cainfo = \"D:\\laragon\\etc\\ssl\\cacert.pem\"' | Set-Content '%%D\php.ini'"
        ) else (
            :: Add new line
            echo curl.cainfo = "D:\laragon\etc\ssl\cacert.pem" >> "%%D\php.ini"
        )
        
        :: Check if openssl.cafile already exists
        findstr /C:"openssl.cafile" "%%D\php.ini" >nul 2>&1
        if %errorLevel% == 0 (
            :: Update existing line
            powershell -Command "(Get-Content '%%D\php.ini') -replace '^;?openssl\.cafile.*', 'openssl.cafile = \"D:\\laragon\\etc\\ssl\\cacert.pem\"' | Set-Content '%%D\php.ini'"
        ) else (
            :: Add new line
            echo openssl.cafile = "D:\laragon\etc\ssl\cacert.pem" >> "%%D\php.ini"
        )
        
        echo     ✓ Updated: %%D\php.ini
        set "php_updated=1"
    )
)

if "%php_updated%" == "0" (
    echo     ⚠ Warning: No PHP installations found in D:\laragon\bin\php\
    echo     Please manually update your php.ini file
)

:: Create test script
echo.
echo Creating SSL test script...
echo ^<?php > "D:\laragon\www\test-ssl.php"
echo echo "Testing SSL Configuration...\n"; >> "D:\laragon\www\test-ssl.php"
echo echo "CA Info: " . ini_get('curl.cainfo') . "\n"; >> "D:\laragon\www\test-ssl.php"
echo echo "OpenSSL CA File: " . ini_get('openssl.cafile') . "\n"; >> "D:\laragon\www\test-ssl.php"
echo echo "\n"; >> "D:\laragon\www\test-ssl.php"
echo $ch = curl_init(); >> "D:\laragon\www\test-ssl.php"
echo curl_setopt($ch, CURLOPT_URL, "https://httpbin.org/get"); >> "D:\laragon\www\test-ssl.php"
echo curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); >> "D:\laragon\www\test-ssl.php"
echo curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); >> "D:\laragon\www\test-ssl.php"
echo curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); >> "D:\laragon\www\test-ssl.php"
echo curl_setopt($ch, CURLOPT_TIMEOUT, 10); >> "D:\laragon\www\test-ssl.php"
echo $result = curl_exec($ch); >> "D:\laragon\www\test-ssl.php"
echo $error = curl_error($ch); >> "D:\laragon\www\test-ssl.php"
echo curl_close($ch); >> "D:\laragon\www\test-ssl.php"
echo if ($error) { >> "D:\laragon\www\test-ssl.php"
echo     echo "CURL Error: " . $error . "\n"; >> "D:\laragon\www\test-ssl.php"
echo } else { >> "D:\laragon\www\test-ssl.php"
echo     echo "✓ SSL Configuration working correctly!\n"; >> "D:\laragon\www\test-ssl.php"
echo     echo "Response length: " . strlen($result) . " bytes\n"; >> "D:\laragon\www\test-ssl.php"
echo } >> "D:\laragon\www\test-ssl.php"
echo ?^> >> "D:\laragon\www\test-ssl.php"
echo     ✓ Test script created: D:\laragon\www\test-ssl.php

echo.
echo ========================================
echo           Setup Completed!
echo ========================================
echo.
echo Next steps:
echo 1. Restart Laragon
echo 2. Restart your terminal/command prompt
echo 3. Test the configuration by running:
echo    php D:\laragon\www\test-ssl.php
echo.
echo Or visit: http://localhost/test-ssl.php
echo.
echo If you encounter any issues, check:
echo - D:\laragon\etc\ssl\cacert.pem exists
echo - PHP.ini files are updated
echo - Environment variables are set
echo.
echo For troubleshooting, see:
echo docs\laragon-ssl-configuration.md
echo.
pause