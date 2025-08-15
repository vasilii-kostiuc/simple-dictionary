#!/bin/bash
set -e

echo "Проверка папок Laravel..."
mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache


if [ ! -f vendor/autoload.php ]; then
    echo "Устанавливаю зависимости Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "Жду MySQL: $DB_HOST:$DB_PORT (база: $DB_DATABASE)..."
until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
    -e "USE $DB_DATABASE;" &> /dev/null
do
  echo "   БД ещё не готова, жду..."
  sleep 2
done
echo "MySQL готов и база $DB_DATABASE доступна!"

echo "Запускаю миграции..."
php artisan migrate

echo "Запуск основного процесса..."
exec "$@"
