# Laragon SSL Configuration Setup Script
# Requires PowerShell 5.0+ and Administrator privileges

param(
    [string]$LaragonPath = "D:\laragon",
    [switch]$Force,
    [switch]$SkipComposer,
    [switch]$Verbose
)

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "This script requires Administrator privileges. Please run PowerShell as Administrator."
    exit 1
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "    Laragon SSL Configuration Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Variables
$sslDir = Join-Path $LaragonPath "etc\ssl"
$certFile = Join-Path $sslDir "cacert.pem"
$certUrl = "https://curl.se/ca/cacert.pem"
$testScriptPath = Join-Path $LaragonPath "www\test-ssl.php"

# Function to write colored output
function Write-Step {
    param([string]$Message, [string]$Status = "INFO")
    switch ($Status) {
        "SUCCESS" { Write-Host "✓ $Message" -ForegroundColor Green }
        "ERROR" { Write-Host "✗ $Message" -ForegroundColor Red }
        "WARNING" { Write-Host "⚠ $Message" -ForegroundColor Yellow }
        "INFO" { Write-Host "ℹ $Message" -ForegroundColor Blue }
        default { Write-Host "$Message" }
    }
}

# Function to test internet connectivity
function Test-InternetConnection {
    try {
        $response = Invoke-WebRequest -Uri "https://www.google.com" -TimeoutSec 5 -UseBasicParsing
        return $true
    } catch {
        return $false
    }
}

# Step 1: Check Laragon installation
Write-Host "[1/7] Checking Laragon installation..." -ForegroundColor Yellow
if (-not (Test-Path $LaragonPath)) {
    Write-Step "Laragon not found at $LaragonPath" "ERROR"
    $customPath = Read-Host "Please enter your Laragon installation path"
    if (Test-Path $customPath) {
        $LaragonPath = $customPath
        $sslDir = Join-Path $LaragonPath "etc\ssl"
        $certFile = Join-Path $sslDir "cacert.pem"
        Write-Step "Using custom path: $LaragonPath" "SUCCESS"
    } else {
        Write-Step "Invalid Laragon path provided" "ERROR"
        exit 1
    }
} else {
    Write-Step "Laragon found at $LaragonPath" "SUCCESS"
}

# Step 2: Create SSL directory
Write-Host ""
Write-Host "[2/7] Creating SSL directory..." -ForegroundColor Yellow
if (-not (Test-Path $sslDir)) {
    try {
        New-Item -ItemType Directory -Path $sslDir -Force | Out-Null
        Write-Step "Directory created: $sslDir" "SUCCESS"
    } catch {
        Write-Step "Failed to create directory: $($_.Exception.Message)" "ERROR"
        exit 1
    }
} else {
    Write-Step "Directory already exists: $sslDir" "SUCCESS"
}

# Step 3: Check internet connection
Write-Host ""
Write-Host "[3/7] Checking internet connection..." -ForegroundColor Yellow
if (-not (Test-InternetConnection)) {
    Write-Step "No internet connection available" "ERROR"
    Write-Step "Please check your internet connection and try again" "ERROR"
    exit 1
} else {
    Write-Step "Internet connection available" "SUCCESS"
}

# Step 4: Download certificate bundle
Write-Host ""
Write-Host "[4/7] Downloading CA certificate bundle..." -ForegroundColor Yellow
if ((Test-Path $certFile) -and (-not $Force)) {
    $choice = Read-Host "Certificate file already exists. Overwrite? (y/N)"
    if ($choice -ne 'y' -and $choice -ne 'Y') {
        Write-Step "Skipping certificate download" "INFO"
    } else {
        $Force = $true
    }
}

if (-not (Test-Path $certFile) -or $Force) {
    try {
        Write-Step "Downloading from: $certUrl" "INFO"
        Invoke-WebRequest -Uri $certUrl -OutFile $certFile -TimeoutSec 30
        
        # Verify download
        $fileInfo = Get-Item $certFile
        if ($fileInfo.Length -gt 100000) {
            Write-Step "Certificate downloaded successfully (Size: $($fileInfo.Length) bytes)" "SUCCESS"
        } else {
            Write-Step "Certificate file seems corrupted (Size: $($fileInfo.Length) bytes)" "ERROR"
            Remove-Item $certFile -Force
            exit 1
        }
    } catch {
        Write-Step "Failed to download certificate: $($_.Exception.Message)" "ERROR"
        exit 1
    }
} else {
    $fileInfo = Get-Item $certFile
    Write-Step "Using existing certificate (Size: $($fileInfo.Length) bytes)" "SUCCESS"
}

# Step 5: Set environment variable
Write-Host ""
Write-Host "[5/7] Setting environment variable..." -ForegroundColor Yellow
try {
    [Environment]::SetEnvironmentVariable("CURL_CA_BUNDLE", $certFile, "Machine")
    Write-Step "Environment variable CURL_CA_BUNDLE set" "SUCCESS"
} catch {
    Write-Step "Failed to set environment variable: $($_.Exception.Message)" "WARNING"
}

# Step 6: Configure Composer
Write-Host ""
Write-Host "[6/7] Configuring Composer..." -ForegroundColor Yellow
if (-not $SkipComposer) {
    try {
        $composerPath = Get-Command composer -ErrorAction SilentlyContinue
        if ($composerPath) {
            & composer config --global cafile $certFile 2>$null
            if ($LASTEXITCODE -eq 0) {
                Write-Step "Composer configured with SSL certificate" "SUCCESS"
            } else {
                Write-Step "Failed to configure Composer" "WARNING"
            }
        } else {
            Write-Step "Composer not found in PATH" "WARNING"
        }
    } catch {
        Write-Step "Error configuring Composer: $($_.Exception.Message)" "WARNING"
    }
} else {
    Write-Step "Skipping Composer configuration" "INFO"
}

# Step 7: Update PHP configuration
Write-Host ""
Write-Host "[7/7] Updating PHP configuration..." -ForegroundColor Yellow
$phpDir = Join-Path $LaragonPath "bin\php"
$phpUpdated = $false

if (Test-Path $phpDir) {
    $phpVersions = Get-ChildItem -Path $phpDir -Directory -Name "php-*"
    
    foreach ($version in $phpVersions) {
        $phpIniPath = Join-Path $phpDir "$version\php.ini"
        
        if (Test-Path $phpIniPath) {
            Write-Step "Updating: $phpIniPath" "INFO"
            
            try {
                # Backup original php.ini
                $backupPath = "$phpIniPath.backup.$(Get-Date -Format 'yyyyMMdd')"
                if (-not (Test-Path $backupPath)) {
                    Copy-Item $phpIniPath $backupPath
                }
                
                # Read current content
                $content = Get-Content $phpIniPath
                
                # Update or add curl.cainfo
                $curlCaInfoFound = $false
                for ($i = 0; $i -lt $content.Length; $i++) {
                    if ($content[$i] -match '^;?curl\.cainfo') {
                        $content[$i] = "curl.cainfo = `"$($certFile -replace '\\', '\\\\')`""
                        $curlCaInfoFound = $true
                        break
                    }
                }
                if (-not $curlCaInfoFound) {
                    $content += "curl.cainfo = `"$($certFile -replace '\\', '\\\\')`""
                }
                
                # Update or add openssl.cafile
                $opensslCaFileFound = $false
                for ($i = 0; $i -lt $content.Length; $i++) {
                    if ($content[$i] -match '^;?openssl\.cafile') {
                        $content[$i] = "openssl.cafile = `"$($certFile -replace '\\', '\\\\')`""
                        $opensslCaFileFound = $true
                        break
                    }
                }
                if (-not $opensslCaFileFound) {
                    $content += "openssl.cafile = `"$($certFile -replace '\\', '\\\\')`""
                }
                
                # Write updated content
                Set-Content -Path $phpIniPath -Value $content
                Write-Step "Updated: $phpIniPath" "SUCCESS"
                $phpUpdated = $true
                
            } catch {
                Write-Step "Failed to update $phpIniPath : $($_.Exception.Message)" "ERROR"
            }
        }
    }
} else {
    Write-Step "PHP directory not found: $phpDir" "WARNING"
}

if (-not $phpUpdated) {
    Write-Step "No PHP installations found or updated" "WARNING"
    Write-Step "Please manually update your php.ini files" "INFO"
}

# Create test script
Write-Host ""
Write-Host "Creating SSL test script..." -ForegroundColor Yellow
$testScript = @'
<?php
echo "Testing SSL Configuration...\n";
echo "CA Info: " . ini_get('curl.cainfo') . "\n";
echo "OpenSSL CA File: " . ini_get('openssl.cafile') . "\n";
echo "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://httpbin.org/get");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$result = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "CURL Error: " . $error . "\n";
} else {
    echo "✓ SSL Configuration working correctly!\n";
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Response length: " . strlen($result) . " bytes\n";
}
?>
'@

try {
    Set-Content -Path $testScriptPath -Value $testScript
    Write-Step "Test script created: $testScriptPath" "SUCCESS"
} catch {
    Write-Step "Failed to create test script: $($_.Exception.Message)" "WARNING"
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "           Setup Completed!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Restart Laragon"
Write-Host "2. Restart your terminal/PowerShell"
Write-Host "3. Test the configuration by running:"
Write-Host "   php $testScriptPath" -ForegroundColor Green
Write-Host ""
Write-Host "Or visit: http://localhost/test-ssl.php" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration files:" -ForegroundColor Yellow
Write-Host "- Certificate: $certFile"
Write-Host "- Test script: $testScriptPath"
Write-Host "- Environment variable: CURL_CA_BUNDLE"
Write-Host ""
Write-Host "For troubleshooting, see:" -ForegroundColor Yellow
Write-Host "docs\laragon-ssl-configuration.md" -ForegroundColor Green
Write-Host ""

# Optional: Run test immediately
$runTest = Read-Host "Do you want to run the SSL test now? (y/N)"
if ($runTest -eq 'y' -or $runTest -eq 'Y') {
    Write-Host ""
    Write-Host "Running SSL test..." -ForegroundColor Yellow
    try {
        $phpPath = Get-Command php -ErrorAction SilentlyContinue
        if ($phpPath) {
            & php $testScriptPath
        } else {
            Write-Step "PHP not found in PATH. Please restart your terminal and try again." "WARNING"
        }
    } catch {
        Write-Step "Error running test: $($_.Exception.Message)" "ERROR"
    }
}

Write-Host ""
Write-Host "Setup completed successfully!" -ForegroundColor Green