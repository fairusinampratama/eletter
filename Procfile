web: php -d display_errors=1 -d error_reporting=E_ALL -S 0.0.0.0:$PORT -t public
worker: php artisan queue:work --tries=3 --timeout=90
