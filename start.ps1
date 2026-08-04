# ============================================================
# start.ps1 - Mot click chay Laravel dev server
# ============================================================

$ErrorActionPreference = 'Continue'
$rootPath = $PSScriptRoot

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Aurora AI Lecture Notes - Dev Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Detect PHP
$phpPaths = @(
    (Get-Command php -ErrorAction SilentlyContinue).Source,
    "C:\Users\nguye\php\php.exe",
    "C:\php\php.exe",
    "$env:USERPROFILE\php\php.exe"
)

$phpCmd = $null
foreach ($p in $phpPaths) {
    if ($p -and (Test-Path $p)) {
        $phpCmd = $p
        break
    }
}

if (-not $phpCmd) {
    Write-Host "[LOI] PHP chua duoc cai dat!" -ForegroundColor Red
    Write-Host "Chay install-php.ps1 truoc." -ForegroundColor Yellow
    pause
    exit 1
}

# Check vendor
if (-not (Test-Path "$rootPath\vendor")) {
    Write-Host "[!] vendor/ chua co. Chay setup truoc:" -ForegroundColor Yellow
    Write-Host "    powershell -ExecutionPolicy Bypass -File setup.ps1" -ForegroundColor Cyan
    pause
    exit 1
}

# Check database
if (-not (Test-Path "$rootPath\database\database.sqlite")) {
    Write-Host "[!] Database chua tao. Chay migrate..." -ForegroundColor Yellow
    Set-Location $rootPath
    New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null
    & $phpCmd artisan migrate --force --seed 2>&1 | Out-Null
    Write-Host ""
}

Write-Host "Server dang khoi dong..." -ForegroundColor Green
Write-Host "Browser se tu mo: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Nhan Ctrl+C de dung server." -ForegroundColor Yellow
Write-Host ""

Set-Location $rootPath
try {
    # Tu mo browser sau 2 giay
    Start-Job -ScriptBlock {
        Start-Sleep -Seconds 2
        Start-Process "http://localhost:8000"
    } | Out-Null

    # Chay server (blocking)
    & $phpCmd artisan serve --host=127.0.0.1 --port=8000
} catch {
    Write-Host ""
    Write-Host "Server da dung." -ForegroundColor Yellow
    pause
}