@echo off
echo ========================================
echo Running Laravel Migrations and Seeders
echo ========================================
echo.

cd /d "c:\laragon\www\antrian-project"

echo [1/3] Running migrations...
php artisan migrate:fresh --seed

echo.
echo [2/3] Creating storage link...
php artisan storage:link

echo.
echo [3/3] Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo ========================================
echo Done! You can now login with:
echo ========================================
echo Super Admin: superadmin@antrian.com
echo Password: password
echo ========================================
pause
