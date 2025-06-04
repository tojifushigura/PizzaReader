 (cd "$(git rev-parse --show-toplevel)" && git apply --3way <<'EOF' 
diff --git a//dev/null b/indstall.md
index 0000000000000000000000000000000000000000..ba4376d687afdbe6c319fb7c08fcf35b3afb26d8 100644
--- a//dev/null
+++ b/indstall.md
@@ -0,0 +1,137 @@
+# Инструкция по установке и настройке PizzaReader
+
+В этом документе описаны шаги по установке сервера и самого приложения. Инструкция рассчитана на начинающих пользователей.
+
+## Рекомендуемые характеристики сервера
+
+| Параметр             | Минимум        | Рекомендовано |
+|---------------------|---------------|--------------|
+| Процессор (CPU)     | 1 ядро        | 2 ядра и выше|
+| Оперативная память  | 1 ГБ          | 2 ГБ и выше  |
+| Дисковое пространство | 10 ГБ        | 20 ГБ и выше |
+| Операционная система | Ubuntu 22.04 LTS или совместимая |
+
+## 1. Подготовка сервера
+1. Подключитесь по SSH к вашему серверу:
+   ```bash
+   ssh root@IP_СЕРВЕРА
+   ```
+2. Обновите пакеты системы:
+   ```bash
+   apt update && apt upgrade -y
+   ```
+3. Установите необходимые утилиты:
+   ```bash
+   apt install -y git curl fail2ban sudo
+   ```
+4. Создайте пользователя для запуска приложения, например `deploy`, и разрешите ему sudo:
+   ```bash
+   adduser deploy
+   usermod -aG sudo deploy
+   ```
+5. Скопируйте ваш публичный SSH‑ключ на сервер и запретите вход по паролю (в `/etc/ssh/sshd_config` установите `PasswordAuthentication no`). После изменения перезапустите SSH:
+   ```bash
+   systemctl restart ssh
+   ```
+6. Включите защиту fail2ban для sshd. Пример минимального файла `/etc/fail2ban/jail.local`:
+   ```
+   [sshd]
+   enabled = true
+   ```
+   Затем перезапустите службу:
+   ```bash
+   systemctl enable --now fail2ban
+   ```
+
+## 2. Установка зависимостей
+1. Установите PHP 8.2 и необходимые расширения:
+   ```bash
+   apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring \
+      php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip php8.2-mysql
+   ```
+2. Установите Composer (менеджер пакетов PHP):
+   ```bash
+   curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
+   ```
+3. Установите Node.js и npm:
+   ```bash
+   apt install -y nodejs npm
+   ```
+
+## 3. Загрузка и установка PizzaReader
+1. Авторизуйтесь под пользователем `deploy` и перейдите в домашний каталог:
+   ```bash
+   su - deploy
+   cd ~
+   ```
+2. Склонируйте репозиторий:
+   ```bash
+   git clone https://github.com/FedericoHeichou/PizzaReader.git pizzareader
+   cd pizzareader
+   ```
+3. Установите PHP‑зависимости без пакетов для разработки:
+   ```bash
+   composer install --no-dev
+   ```
+4. Скопируйте пример файла настроек и сгенерируйте ключ приложения:
+   ```bash
+   cp .env.example .env
+   php artisan key:generate
+   ```
+5. Установите JavaScript‑зависимости и соберите фронтенд:
+   ```bash
+   npm install
+   npm run production
+   ```
+6. Создайте символьную ссылку для хранения загружаемых файлов и задайте права:
+   ```bash
+   php artisan storage:link
+   chmod -R 775 storage bootstrap/cache
+   ```
+7. Выполните миграции и наполните базу тестовыми данными:
+   ```bash
+   php artisan migrate --seed
+   ```
+
+## 4. Настройка веб‑сервера
+1. Настройте веб‑сервер (Apache или Nginx), чтобы корневая директория сайта указывала на каталог `public` проекта. Ниже пример для Nginx:
+   ```nginx
+   server {
+       listen 80;
+       server_name example.com;
+       root /home/deploy/pizzareader/public;
+
+       add_header X-Frame-Options "SAMEORIGIN";
+       add_header X-Content-Type-Options "nosniff";
+
+       index index.php;
+       charset utf-8;
+
+       location / {
+           try_files $uri $uri/ /index.php?$query_string;
+       }
+
+       location ~ \php$ {
+           include snippets/fastcgi-php.conf;
+           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
+       }
+
+       location ~ /\.ht {
+           deny all;
+       }
+   }
+   ```
+2. Перезапустите веб‑сервер после внесения изменений.
+
+## 5. Проверка работы
+1. Перейдите по адресу вашего сайта в браузере. Если установка прошла успешно, вы увидите стартовую страницу PizzaReader.
+2. Для проверки версии выполните:
+   ```bash
+   php artisan --version
+   ```
+3. Рекомендуется настроить cron или systemd‑таймер для команды расписания Laravel:
+   ```bash
+   * * * * * deploy cd /home/deploy/pizzareader && php artisan schedule:run >> /dev/null 2>&1
+   ```
+
+Инсталляция завершена. При необходимости вы можете изменить настройки в файле `.env` и в админ‑панели приложения.
 
EOF
)
