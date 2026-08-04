@echo off
REM ============================================================
REM start.bat - Mot click chay Laravel dev server (CMD version)
REM ============================================================

echo.
echo ============================================================
echo   Aurora AI Lecture Notes - Dev Server
echo ============================================================
echo.

REM Check PHP
where php >nul 2>&1
if errorlevel 1 (
    echo [LOI] PHP chua duoc cai dat.
    pause
    exit /b 1
)

REM Check vendor
if not exist vendor (
    echo [!] vendor/ chua co. Chay setup truoc.
    pause
    exit /b 1
)

REM Check database
if not exist database\database.sqlite (
    echo [!] Database chua tao. Chay migrate...
    if not exist database mkdir database
    type nul > database\database.sqlite
    call php artisan migrate --force --seed >nul
)

echo Server dang khoi dong...
echo Truy cap: http://localhost:8000
echo Nhan Ctrl+C de dung.
echo.

REM Tu mo browser sau 2 giay
start /min cmd /c "timeout /t 2 >nul && start http://localhost:8000"

REM Chay server (blocking)
php artisan serve --host=127.0.0.1 --port=8000

pause
