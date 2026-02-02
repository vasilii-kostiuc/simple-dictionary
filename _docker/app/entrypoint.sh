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

until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" --password="$DB_PASSWORD" \
       --ssl=0 -e "SELECT 1;" &> /dev/null
do
  echo "Ожидание запуска MySQL..."
  echo "$DB_HOST : $DB_PORT : $DB_USERNAME : $DB_PASSWORD"
  sleep 2
done

# Как только сервер поднялся — проверяем базу
if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" --password="$DB_PASSWORD" --ssl=0 \
    -e "SHOW DATABASES LIKE '$MYSQL_DATABASE';" | grep -q "$MYSQL_DATABASE"; then
  echo "База данных $MYSQL_DATABASE найдена и доступна!"
else
  echo "База данных $MYSQL_DATABASE не существует!"
fi

echo "MySQL готов и база $MYSQL_DATABASE доступна!"


echo "Запускаю миграции..."
php artisan migrate --force

echo "Запуск основного процесса..."
exec "$@"
