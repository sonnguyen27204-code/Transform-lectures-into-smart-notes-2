@echo off
REM ============================================================
REM Aurora AI Lecture Notes - Setup Script (CMD version)
REM ============================================================

echo.
echo ============================================================
echo   Aurora AI Lecture Notes - Setup (SQLite)
echo ============================================================
echo.

REM Check PHP
where php >nul 2>&1
if errorlevel 1 (
    echo [LOI] PHP chua duoc cai dat.
    echo Hay chay file install-php.ps1 voi quyen Administrator.
    pause
    exit /b 1
)

echo PHP version:
php -r 'echo PHP_VERSION;'
echo.

REM Check Composer
where composer >nul 2>&1
if errorlevel 1 (
    echo [LOI] Composer chua duoc cai dat.
    echo Tai tai: https://getcomposer.org/Composer-Setup.exe
    pause
    exit /b 1
)

echo [1/5] Cai dat Composer dependencies...
if exist vendor (
    echo   [SKIP] vendor/ da ton tai
) else (
    call composer install --no-interaction --prefer-dist >nul 2>&1
    if errorlevel 1 (
        echo   [LOI] Cai dat that bai.
        pause
        exit /b 1
    )
)
echo   [OK]

echo [2/5] Build assets (optional)...
where npm >nul 2>&1
if errorlevel 1 (
    echo   [SKIP] npm khong co
) else (
    if not exist node_modules call npm install >nul 2>&1
    call npm run build >nul 2>&1
)

echo [3/5] Setup .env...
if not exist .env (
    copy .env.example .env >nul
    call php artisan key:generate --force
)
echo   [OK]

echo [4/5] Tao SQLite database...
if not exist database\database.sqlite (
    if not exist database mkdir database
    type nul > database\database.sqlite
)
call php artisan migrate --force --seed
if errorlevel 1 (
    echo   [LOI] Migration that bai.
    pause
    exit /b 1
)
echo   [OK]

echo [5/5] Storage symlink...
call php artisan storage:link >nul 2>&1
echo   [OK]

echo.
echo ============================================================
echo   SETUP HOAN TAT!
echo ============================================================
echo.
echo   Tai khoan demo:
echo     Email:    demo@aurora.app
echo     Password: password
echo.
echo   Chay server:
echo     php artisan serve
echo.
echo   Truy cap: http://localhost:8000
echo.
pause
