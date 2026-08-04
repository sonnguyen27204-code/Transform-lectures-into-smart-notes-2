# ============================================================
# Aurora AI Lecture Notes - Setup Script (SQLite version)
# ============================================================

$ErrorActionPreference = 'Continue'
$rootPath = $PSScriptRoot

Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "  Aurora AI Lecture Notes - Setup Script" -ForegroundColor Cyan
Write-Host "  (Dung SQLite - khong can cai MySQL)" -ForegroundColor DarkCyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# ============================================================
# Detect PHP - thu cac vi tri co the
# ============================================================
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
    Write-Host "[LOI] PHP chua duoc cai dat hoac chua co trong PATH." -ForegroundColor Red
    Write-Host ""
    Write-Host "Ban co 2 lua chon:" -ForegroundColor Yellow
    Write-Host "  1. Chay script install-php.ps1:" -ForegroundColor White
    Write-Host "     powershell -ExecutionPolicy Bypass -File install-php.ps1" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "  2. Tai thu cong:" -ForegroundColor White
    Write-Host "     https://windows.php.net/download/" -ForegroundColor Cyan
    Write-Host ""
    pause
    exit 1
}

$phpVersion = & $phpCmd -r 'echo PHP_VERSION;' 2>$null
Write-Host "  PHP version: $phpVersion ($phpCmd)" -ForegroundColor Green

# ============================================================
# Detect Composer
# ============================================================
$composerPaths = @(
    (Get-Command composer -ErrorAction SilentlyContinue).Source,
    "C:\Users\nguye\composer\composer.bat",
    "C:\composer\composer.bat",
    "$env:USERPROFILE\composer\composer.bat"
)

$composerCmd = $null
foreach ($p in $composerPaths) {
    if ($p -and (Test-Path $p)) {
        $composerCmd = $p
        break
    }
}

if (-not $composerCmd) {
    Write-Host "[LOI] Composer chua duoc cai dat." -ForegroundColor Red
    Write-Host "  Tai tai: https://getcomposer.org/Composer-Setup.exe" -ForegroundColor Yellow
    pause
    exit 1
}
$composerVersion = & $composerCmd --version 2>$null | Select-Object -First 1
Write-Host "  Composer: $composerVersion" -ForegroundColor Green

Write-Host ""

# ============================================================
# Step 1: Install composer dependencies
# ============================================================
Write-Host "[1/6] Cai dat Composer dependencies..." -ForegroundColor Yellow
Set-Location $rootPath
if (Test-Path "vendor") {
    Write-Host "  [SKIP] vendor/ da ton tai" -ForegroundColor Gray
} else {
    & $composerCmd install --no-interaction --prefer-dist 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  [LOI] Cai dat that bai. Kiem tra mang." -ForegroundColor Red
        pause
        exit 1
    }
}
Write-Host "  [OK] Composer dependencies" -ForegroundColor Green
Write-Host ""

# ============================================================
# Step 2: Install node + build (optional)
# ============================================================
Write-Host "[2/6] Build Tailwind CSS..." -ForegroundColor Yellow
$nodeCmd = Get-Command npm -ErrorAction SilentlyContinue
if (-not $nodeCmd) {
    Write-Host "  [SKIP] npm khong co - bo qua build assets" -ForegroundColor Yellow
} else {
    if (Test-Path "node_modules") {
        Write-Host "  [SKIP] node_modules/ da ton tai" -ForegroundColor Gray
    } else {
        & npm install 2>&1 | Out-Null
    }
    & npm run build 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  [OK] Tailwind CSS da duoc build" -ForegroundColor Green
    } else {
        Write-Host "  [WARN] Build that bai, nhung van tiep tuc" -ForegroundColor Yellow
    }
}
Write-Host ""

# ============================================================
# Step 3: Setup .env
# ============================================================
Write-Host "[3/6] Setup file .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "  [OK] Da tao .env" -ForegroundColor Green
} else {
    Write-Host "  [SKIP] .env da ton tai" -ForegroundColor Gray
}

Write-Host "  Generate APP_KEY..." -ForegroundColor White
& $phpCmd artisan key:generate --force 2>&1 | Out-Null
Write-Host "  [OK] APP_KEY da san sang" -ForegroundColor Green
Write-Host ""

# ============================================================
# Step 4: Create SQLite database file
# ============================================================
Write-Host "[4/6] Tao SQLite database..." -ForegroundColor Yellow
$dbPath = Join-Path $rootPath "database\database.sqlite"
if (Test-Path $dbPath) {
    Write-Host "  [SKIP] database.sqlite da ton tai" -ForegroundColor Gray
} else {
    if (-not (Test-Path "database")) { New-Item -ItemType Directory -Path "database" | Out-Null }
    New-Item -ItemType File -Path $dbPath | Out-Null
    Write-Host "  [OK] Tao file $dbPath" -ForegroundColor Green
}
Write-Host ""

# ============================================================
# Step 5: Run migrations + seed
# ============================================================
Write-Host "[5/6] Chay migration + seed..." -ForegroundColor Yellow
& $phpCmd artisan migrate --force --seed 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "  [LOI] Migration that bai!" -ForegroundColor Red
    Write-Host "  Kiem tra loi:" -ForegroundColor Yellow
    & $phpCmd artisan migrate --force --seed
    pause
    exit 1
}
Write-Host "  [OK] Database da san sang" -ForegroundColor Green
Write-Host ""

# ============================================================
# Step 6: Storage symlink
# ============================================================
Write-Host "[6/6] Tao storage symlink..." -ForegroundColor Yellow
& $phpCmd artisan storage:link 2>&1 | Out-Null
Write-Host "  [OK] Storage" -ForegroundColor Green
Write-Host ""

# ============================================================
# Warning GEMINI
# ============================================================
$envContent = Get-Content ".env" -Raw
if ($envContent -match "GEMINI_API_KEY=\s*$") {
    Write-Host "============================================================" -ForegroundColor Magenta
    Write-Host "  CANH BAO: GEMINI_API_KEY chua duoc cau hinh!" -ForegroundColor Magenta
    Write-Host "  Lay key mien phi tai: https://aistudio.google.com/app/apikey" -ForegroundColor White
    Write-Host "  Sua file .env va them: GEMINI_API_KEY=AIza...your_key" -ForegroundColor White
    Write-Host "  (UI van chay duoc, upload se bi loi)" -ForegroundColor DarkMagenta
    Write-Host "============================================================" -ForegroundColor Magenta
    Write-Host ""
}

Write-Host "============================================================" -ForegroundColor Green
Write-Host "  SETUP HOAN TAT!" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Tai khoan demo:" -ForegroundColor Cyan
Write-Host "    Email:    demo@aurora.app" -ForegroundColor White
Write-Host "    Password: password" -ForegroundColor White
Write-Host ""
Write-Host "  Chay server:" -ForegroundColor Cyan
Write-Host "    powershell -ExecutionPolicy Bypass -File start.ps1" -ForegroundColor White
Write-Host ""
Write-Host "  Truy cap: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
pause