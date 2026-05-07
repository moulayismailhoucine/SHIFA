@echo off
chcp 65001 >nul
cd /d "C:\Users\H\Desktop\project\medisys"

echo ==========================================
echo  Démarrage rapide du serveur Laravel
echo ==========================================
echo.

:: Vérifier si vendor existe
if not exist "vendor" (
    echo [ERREUR] Les dépendances PHP ne sont pas installées.
    echo.
    echo Veuillez d'abord exécuter : composer install
    echo Ou attendez que l'autre script termine.
    pause
    exit /b 1
)

:: Vérifier si database.sqlite existe
if not exist "database\database.sqlite" (
    echo [INFO] Création de la base de données SQLite...
    if not exist "database" mkdir database
    type nul > database\database.sqlite
)

:: Vérifier si .env existe
if not exist ".env" (
    echo [INFO] Création du fichier .env...
    copy .env.example .env >nul
    php artisan key:generate --force
)

:: Vérifier si les tables existent
echo [INFO] Vérification des migrations...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo [INFO] Exécution des migrations et seeders...
    php artisan migrate:fresh --seed --force
)

echo.
echo ==========================================
echo  Le serveur démarre sur :
echo  http://127.0.0.1:8000
echo ==========================================
echo.
echo Login: admin@shifa.local / Admin@1234
echo.
echo Appuyez sur Ctrl+C pour arrêter
echo.

php artisan serve --host=127.0.0.1 --port=8000
pause
