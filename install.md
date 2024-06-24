sudo apt update
sudo apt upgrade -y

sudo apt install -y apache2 php7.4 libapache2-mod-php7.4 php7.4-mysql php7.4-curl php7.4-json php7.4-mbstring php7.4-xml composer unzip git

sudo apt install -y software-properties-common
// sudo apt-get purge php8.*
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-mysql php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip php8.2-imagick php8.2-gd

sudo apt install -y mysql-server

sudo mysql
CREATE DATABASE pizzareader;
CREATE USER 'manga'@'localhost' IDENTIFIED BY 'mI1oE6sG3ciU';
GRANT ALL PRIVILEGES ON *.* TO 'manga'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;


sudo a2enmod rewrite
sudo systemctl restart apache2

cd /var/www/
git clone https://github.com/FedericoHeichou/PizzaReader.git pizzareader
cd pizzareader

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
cd public; ln -s ../storage/app/public storage
cd ..

php --ini
php composer.phar install --no-dev
php composer.phar install --no-dev --ignore-platform-req=ext-zip --ignore-platform-req=ext-imagick --ignore-platform-req=ext-gd

cp .env.example .env
nano .env

php composer.phar dump-autoload
php artisan key:generate
php artisan migrate
php artisan db:seed

php artisan config:cache
php composer.phar dump-autoload


find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;


/etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
DocumentRoot /var/www/pizzareader/public

<Directory /var/www/pizzareader/public>
AllowOverride All
Require all granted
</Directory>

# ... остальные настройки ...
</VirtualHost>
——————————————-
sudo chown -R www-data:www-data /var/www/pizzareader
sudo chmod -R 755 /var/www/pizzareader

sudo systemctl restart apache2


крон потом
* * * * * cd /path/to/pizzareader && /usr/bin/php artisan schedule:run > /dev/null 2>&1
21:27
Чтобы удалить версию PHP 7.4 с Ubuntu, вам нужно выполнить несколько шагов. Пожалуйста, убедитесь, что вы уверены в том, что не используете эту версию в других проектах, прежде чем удалить её.

Шаг 1: Отключение модуля PHP 7.4
Если вы используете Apache:

bash
Copy code
sudo a2dismod php7.4
Если вы используете Nginx, нужно удалить пакет PHP 7.4 для Nginx:

bash
Copy code
sudo apt-get purge php7.4-fpm
Шаг 2: Удаление PHP 7.4
bash
Copy code
sudo apt-get purge php7.4
Шаг 3: Обновление конфигурации Apache (если используете)
bash
Copy code
sudo service apache2 restart

--

sudo a2dismod php7.4
sudo a2enmod php8.2
sudo service apache2 restart




cd /var/www/wamanga.ru
php artisan cache:clear
sudo service apache2 restart

sudo systemctl restart apache2

<VirtualHost *:80>
ServerName wamanga.ru
ServerAlias www.wamanga.ru
DocumentRoot /var/www/wamanga.ru/public

<Directory /var/www/wamanga.ru/public>
AllowOverride All
Require all granted
</Directory>


# Редирект на HTTPS
Redirect permanent / https://pizzareader/

# Блокировка доступа по IP
RedirectMatch 403 /\d+\.\d+\.\d+\.\d+


# ... остальные настройки ...
RewriteEngine on
RewriteCond %{HTTP_HOST} !^pizzareader\.ru$ [NC]
RewriteRule ^ https://pizzareader.ru%{REQUEST_URI} [L,R=301]

ErrorLog ${APACHE_LOG_DIR}/error.log
CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>

<VirtualHost *:443>
ServerName wamanga.ru
ServerAlias www.wamanga.ru
DocumentRoot /var/www/pizzareader.ru/public

<Directory /var/www/pizzareader.ru/public>
AllowOverride All
Require all granted
</Directory>

# ... другие настройки для HTTPS ...
</VirtualHost>

<VirtualHost *:80>
ServerName 82.97.240.99
Redirect 403 /
</VirtualHost>

<VirtualHost *:443>
ServerName 82.97.240.99
Redirect 403 /
</VirtualHost>

php composer.phar install --no-dev --ignore-platform-req=ext-*
