@echo off
echo ========================================
echo Hairdresser Booking System - Setup
echo ========================================
echo.

echo [1/8] Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 goto :error

echo.
echo [2/8] Copying environment file...
if not exist .env (
    copy .env.example .env
    echo .env file created. Please update database credentials!
) else (
    echo .env already exists, skipping...
)

echo.
echo [3/8] Generating application key...
call php artisan key:generate
if %errorlevel% neq 0 goto :error

echo.
echo [4/8] Clearing config cache...
call php artisan config:clear
if %errorlevel% neq 0 goto :error

echo.
echo [5/8] Running database migrations...
call php artisan migrate
if %errorlevel% neq 0 (
    echo.
    echo WARNING: Migrations failed. Make sure database is configured in .env
    echo.
)

echo.
echo [6/8] Seeding hairdresser user...
call php artisan db:seed --class=HairdresserSeeder
if %errorlevel% neq 0 (
    echo.
    echo WARNING: Seeding failed. You may need to create user manually.
    echo.
)

echo.
echo [7/8] Installing npm dependencies...
call npm install
if %errorlevel% neq 0 goto :error

echo.
echo [8/8] Building frontend assets...
call npm run build
if %errorlevel% neq 0 goto :error

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Default login credentials:
echo   Email: hairdresser@example.com
echo   Password: password
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo Then visit: http://localhost:8000
echo ========================================

goto :end

:error
echo.
echo ========================================
echo ERROR: Setup failed!
echo ========================================
echo Please check the error messages above.
pause
exit /b 1

:end
pause

