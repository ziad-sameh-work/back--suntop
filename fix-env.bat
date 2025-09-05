@echo off
echo 🔧 FIXING .ENV CONFIGURATION
echo ============================

if not exist .env (
    echo ❌ .env file not found!
    echo 📋 Copying .env.example to .env...
    copy .env.example .env
)

echo 📝 Updating BROADCAST_DRIVER to pusher...
powershell -Command "(Get-Content .env) -replace 'BROADCAST_DRIVER=.*', 'BROADCAST_DRIVER=pusher' | Set-Content .env"

echo 📝 Ensuring Pusher credentials are set...
findstr /C:"PUSHER_APP_ID=" .env >nul
if errorlevel 1 (
    echo PUSHER_APP_ID=2046066 >> .env
)

findstr /C:"PUSHER_APP_KEY=" .env >nul
if errorlevel 1 (
    echo PUSHER_APP_KEY=f546bf192457a6d47ed5 >> .env
)

findstr /C:"PUSHER_APP_SECRET=" .env >nul
if errorlevel 1 (
    echo PUSHER_APP_SECRET=d1a687b90b02f69ea917 >> .env
)

findstr /C:"PUSHER_APP_CLUSTER=" .env >nul
if errorlevel 1 (
    echo PUSHER_APP_CLUSTER=eu >> .env
)

echo 🧹 Clearing Laravel config cache...
php artisan config:clear
php artisan config:cache

echo ✅ Configuration updated!
echo 🎯 Now test with: php test-realtime-chat.php customer "test after fix"

pause
