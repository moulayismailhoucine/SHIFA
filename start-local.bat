@echo off
chcp 65001 >nul
echo ==========================================
echo  Démarrage du projet MediSys en local
echo ==========================================
echo.

:: Vérifier si on est dans le bon dossier
if not exist "artisan" (
    echo ERREUR: Veuillez exécuter ce script depuis le dossier du projet Laravel!
    pause
    exit /b 1
)

:: 1. Installer les dépendances PHP
echo [1/7] Installation des dépendances PHP...
if not exist "vendor" (
    composer install --no-dev --optimize-autoloader
    if errorlevel 1 (
        echo ERREUR: composer install a échoué
        pause
        exit /b 1
    )
) else (
    echo Les dépendances PHP existent déjà.
)

:: 2. Créer .env s'il n'existe pas
echo [2/7] Configuration de l'environnement...
if not exist ".env" (
    copy .env.example .env
    echo Fichier .env créé.
)

:: 3. Configurer SQLite dans .env
echo [3/7] Configuration de SQLite...
echo DB_CONNECTION=sqlite> .env.tmp
echo DB_DATABASE=database/database.sqlite>> .env.tmp
echo DB_FOREIGN_KEYS=true>> .env.tmp
type .env >> .env.tmp
del .env
move .env.tmp .env >nul

:: Créer le dossier database et le fichier SQLite
if not exist "database" mkdir database
if not exist "database\database.sqlite" (
    type nul > database\database.sqlite
    echo Fichier SQLite créé.
)

:: 4. Générer la clé APP
echo [4/7] Génération de la clé APP...
php artisan key:generate --force

:: 5. Exécuter migrations et seeders
echo [5/7] Exécution des migrations et seeders...
php artisan migrate:fresh --seed --force
if errorlevel 1 (
    echo ERREUR: Les migrations ont échoué
    pause
    exit /b 1
)

:: 6. Installer Node.js et compiler
echo [6/7] Installation des dépendances Node.js et compilation...
if exist "package.json" (
    if not exist "node_modules" (
        npm install
        if errorlevel 1 (
            echo AVERTISSEMENT: npm install a échoué, mais on continue...
        )
    )
    npm run build 2>nul || echo Compilation ignorée (pas critique pour le développement)
)

:: 7. Démarrer le serveur
echo [7/7] Démarrage du serveur Laravel...
echo.
echo ==========================================
echo  Le serveur démarre sur http://127.0.0.1:8000
echo ==========================================
echo.
echo Login Admin:
echo   Email: admin@shifa.local
echo   Password: Admin@1234
echo.
echo Appuyez sur Ctrl+C pour arrêter le serveur
echo.

php artisan serve --host=127.0.0.1 --port=8000

pause
