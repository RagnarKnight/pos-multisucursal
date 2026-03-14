#crear proyecto
composer create-project laravel/laravel sistema-santa-fe
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build



composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
