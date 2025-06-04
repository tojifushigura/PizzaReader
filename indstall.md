# Инструкция по установке PizzaReader на сервер

## Требования к серверу

Минимальные характеристики сервера приведены в таблице:

| Параметр          | Значение                       | Примечание |
|-------------------|--------------------------------|------------|
| ОС                | Ubuntu Server 22.04 LTS        | Рекомендуемая версия |
| Процессор         | 2 ядра                         | Возможен виртуальный сервер |
| Оперативная память| 2&nbsp;ГБ и более              | Для небольших проектов достаточно 2&nbsp;ГБ |
| Дисковое пространство | 20&nbsp;ГБ и более          | Зависит от объёма загружаемых файлов |
| Пользователь      | `pizzareader`                  | Отдельный системный пользователь для приложения |

## Подготовка сервера

1. Подключитесь к серверу по SSH:
   ```bash
   ssh user@your-server-ip
   ```
2. Обновите систему и установите необходимые утилиты:
   ```bash
   sudo apt update && sudo apt -y upgrade
   sudo apt install -y git unzip curl fail2ban
   ```
3. Настройте вход по SSH‑ключу и запретите вход по паролю:
   ```bash
   sudo sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication no/' /etc/ssh/sshd_config
   sudo systemctl restart ssh
   ```
   Убедитесь, что ваш публичный ключ добавлен в `~/.ssh/authorized_keys`.
4. Включите fail2ban для защиты SSH:
   ```bash
   sudo systemctl enable --now fail2ban
   sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
   sudo systemctl restart fail2ban
   ```
5. Создайте пользователя для приложения и войдите под ним:
   ```bash
   sudo adduser pizzareader
   sudo usermod -aG sudo pizzareader
   sudo su - pizzareader
   ```

## Установка PHP и Composer

1. Установите PHP и расширения:
   ```bash
   sudo apt install -y php php-mbstring php-xml php-curl php-zip php-gd php-mysql php-cli php-bcmath
   ```
2. Скачайте и установите Composer:
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   mv composer.phar ~/bin/composer
   chmod +x ~/bin/composer
   ```
   Убедитесь, что `~/bin` находится в переменной `PATH` или добавьте её в `~/.profile`.

## Загрузка и настройка PizzaReader

1. Перейдите в каталог для веб‑сайтов и скачайте репозиторий:
   ```bash
   mkdir -p ~/web && cd ~/web
   git clone https://github.com/FedericoHeichou/PizzaReader.git pizzareader
   cd pizzareader
   ```
2. Создайте символьную ссылку для хранилища и установите зависимости:
   ```bash
   cd public && ln -s ../storage/app/public storage
   cd ..
   composer install --no-dev
   ```
3. Скопируйте файл настроек и отредактируйте значения в `.env`:
   ```bash
   cp .env.example .env
   # далее измените параметры: APP_URL, DB_HOST, DB_DATABASE и т.д.
   ```
   Основные переменные:
   - `APP_URL` – адрес сайта, например `https://reader.example.com`;
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` – данные для подключения к MySQL.
4. Сгенерируйте ключ приложения и выполните миграции:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan config:cache
   ```
5. Установите корректные права доступа:
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   ```

## Запуск

Настройте веб‑сервер (Nginx или Apache) на папку `public` в каталоге `pizzareader`. После этого сайт будет доступен по адресу, указанному в `APP_URL`.

Готово! Теперь вы можете перейти по адресу сайта и зарегистрироваться в панели администрирования `/admin`.
