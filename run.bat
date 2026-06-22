@echo off
echo ========================================
echo Hairdresser Booking - Quick Test
echo ========================================
echo.

echo Checking PHP version...
php -v | findstr /C:"PHP"
echo.

echo Checking Composer...
composer --version | findstr /C:"Composer"
echo.

echo Checking Node.js...
node -v
echo.

echo Checking NPM...
npm -v
echo.

echo Checking MySQL connection...
php artisan migrate:status
echo.

echo Checking routes...
php artisan route:list | findstr /C:"bookings"
php artisan route:list | findstr /C:"admin"
echo.

echo ========================================
echo Starting development server...
echo ========================================
echo.
echo Access the application at:
echo   - Homepage: http://localhost:8000
echo   - Login: http://localhost:8000/login
echo   - Dashboard: http://localhost:8000/admin/dashboard
echo.
echo Login credentials:
echo   Email: hairdresser@example.com
echo   Password: password
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve

