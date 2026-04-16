# Deployment Guide - Laravel API на Hetzner

## 🎯 Архитектура

- **GitHub Actions** - автоматическая сборка и деплой
- **GitHub Container Registry** - хранение Docker образов
- **Docker Compose** - оркестрация контейнеров на сервере
- **Nginx** - reverse proxy и статика

## 📋 Подготовка сервера Hetzner

### 1. Первоначальная настройка сервера

```bash
# Подключитесь к серверу
ssh root@your-server-ip

# Обновите систему
apt update && apt upgrade -y

# Установите Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Установите Docker Compose
apt install docker-compose-plugin -y

# Создайте пользователя для деплоя (опционально, можно использовать root)
adduser deploy
usermod -aG docker deploy
```

### 2. Настройка директорий на сервере

```bash
# Создайте структуру директорий
mkdir -p /var/www/laravel-api
cd /var/www/laravel-api

# Скопируйте docker-compose.prod.yml и nginx конфиги
# (можно сделать через git clone или scp)
```

### 3. Настройка .env файла

```bash
# Создайте .env файл на сервере
nano /var/www/laravel-api/.env

# Скопируйте содержимое из .env.production.example
# Заполните реальные данные (пароли, ключи и т.д.)

# Сгенерируйте APP_KEY
docker run --rm ghcr.io/vasilii-kostiuc/simple-dictionari:latest php artisan key:generate --show
```

### 4. Настройка SSL (Let's Encrypt)

```bash
# Установите Certbot
apt install certbot python3-certbot-nginx -y

# Получите SSL сертификат
certbot certonly --standalone -d api.yourdomain.com

# Сертификаты будут в /etc/letsencrypt/live/api.yourdomain.com/
```

### 5. Настройка Nginx на хосте (опционально)

Если хотите использовать Nginx на хосте как reverse proxy:

```bash
nano /etc/nginx/sites-available/laravel-api
```

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.yourdomain.com;

    ssl_certificate /etc/letsencrypt/live/api.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.yourdomain.com/privkey.pem;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Активируйте конфигурацию
ln -s /etc/nginx/sites-available/laravel-api /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

## 🔑 Настройка GitHub Secrets

Перейдите в ваш репозиторий на GitHub:
**Settings → Secrets and variables → Action s → New reposito----- r y secret**---------------- -------------------------

Добав------- ьте следующие secr ets:

### Обязательные:

| Secret Name                     | О писание                  | Пример                                  |
| ------------------------------- | -------------------------- | --------------------------------------- |
| `                  SERVER_HOST` | IP адрес или домен сервера | `123.45.67.89`                          |
| `SERVER_USER`                   | Пользователь SSH           | `root` или `deploy`                     |
| `SSH_PRIVATE_KEY`               | Приватн ый SSH ключ -      | `-----B I OPENSSH PRIV ATE KEY-----...` |

### Опциональ н ые:

| Secret Name   | Описание | По умолчанию |
| ------------- | -------- | ------------ |
| `SERVER_PORT` | SSH порт | `22`         |

## 🔐 Настройка SSH ключей

### На вашей локальной машине:

```bash
# Сгенерируйте новую пару ключей (если еще нет)
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_deploy

# Скопируйте публичный ключ на сервер
ssh-copy-id -i ~/.ssh/github_deploy.pub root@your-server-ip

# Проверьте подключение
ssh -i ~/.ssh/github_deploy root@your-server-ip
```

### Добавьте приватный ключ в GitHub Secrets:

```bash
# Выведите приватный ключ
cat ~/.ssh/github_deploy

# Скопируйте весь вывод (включая BEGIN и END строки)
# И вставьте в GitH ub Secret SSH_PRIVATE_KEY
```

## 🚀 Процесс деплоя

### Автомати ческий деплой:

1. Push в ветку ` main` или `master`
2. GitHub Actions автоматически:
    - Собирает Docker образ
    - Загружает в GitHub Container Registry
    - Подключается к серверу по SSH
    - Скачивает новый образ
    - Перезапускает контейнеры
    - Запускает миграции
    - Очищает кэш

### Ручной деплой:

```bash
# В GitHub: Actions → Deploy to Hetzner → Run workflow
```

### Деплой с сервера вручную:

```bash
ssh root@your-server-ip
cd /var/www/laravel-api

# Авторизуйтесь в Registry
echo "YOUR_GITHUB_TOKEN" | docker login ghcr.io -u YOUR_GITHUB_USERNAME --password-stdin

# Скачайте образ и перезапустите
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d

# Запустите миграции
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

## 🔍 Мониторинг и логи

```bash
# Просмотр логов всех контейнеров
docker-compose -f docker-compose.prod.yml logs -f

# Логи конкретного сервиса
docker-compose -f docker-compose.prod.yml logs -f app

# Статус контейнеров
docker-compose -f docker-compose.prod.yml ps

# Зайти в контейнер
docker-compose -f docker-compose.prod.yml exec app bash
```

## 🛠 Troubleshooting

### Проблема: Контейнер не запускается

```bash
# Проверьте логи
docker-compose -f docker-compose.prod.yml logs app

# Проверьте .env файл
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
```

### Проблема: Ошибки миграций

```bash
# Откатите последнюю миграцию
docker-compose -f docker-compose.prod.yml exec app php artisan migrate:rollback

# Проверьте подключение к БД
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
# >>> DB::connection()->getPdo();
```

### Проблема: Недостаточно места на диске

```bash
# Очистите старые образы
docker system prune -a --volumes

# Очистите логи Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan log:clear
```

## 📊 Backup стратегия

### Backup базы данных:

```bash
# Создайте скрипт backup.sh
cat > /var/www/laravel-api/backup.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/laravel-api"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

docker-compose -f /var/www/laravel-api/docker-compose.prod.yml exec -T db \
  mysqldump -u root -p$DB_ROOT_PASSWORD simple_dictionary > $BACKUP_DIR/db_$DATE.sql

# Удалить бэкапы старше 7 дней
find $BACKUP_DIR -name "db_*.sql" -mtime +7 -delete
EOF

chmod +x /var/www/laravel-api/backup.sh

# Добавьте в cron (каждый день в 2:00)
crontab -e
# 0 2 * * * /var/www/laravel-api/backup.sh
```

## 🔄 Rollback

```bash
# Просмотрите доступные образы
docker images | grep simple-dictionari

# Откатитесь на предыдущую версию
docker-compose -f docker-compose.prod.yml down
docker tag ghcr.io/vasilii-kostiuc/simple-dictionari:previous ghcr.io/vasilii-kostiuc/simple-dictionari:latest
docker-compose -f docker-compose.prod.yml up -d
```

## 📝 Чеклист после первого деплоя

- [ ] Сервер доступен по SSH
- [ ] Docker и Docker Compose установлены
- [ ] Структура директорий создана
- [ ] .env файл настроен с правильными данными
- [ ] SSL сертификаты установлены
- [ ] GitHub Secrets добавлены
- [ ] SSH ключи настроены
- [ ] Firewall настроен (порты 80, 443, 22 открыты)
- [ ] Первый деплой прошел успешно
- [ ] База данных мигрирована
- [ ] API отвечает на запросы
- [ ] Backup настроен

## 🌐 Дополнительные компоненты

Для интеграции с **WebSocket сервером** и **Vue.js клиентом**, создайте аналогичные workflow в их репозиториях, следуя этому же паттерну.
