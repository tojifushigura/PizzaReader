# <p align="center">![PizzaReader Logo](storage/app/public/img/logo/PizzaReader-128.png)<br />PizzaReader</p>
<p align="center">
    <img alt="Latest version" src="https://img.shields.io/badge/stable-v1.6.x-blue">
    <img alt="PHP Version Support" src="https://img.shields.io/badge/php-%3E%3D8.2-blue">
    <img alt="Laravel version" src="https://img.shields.io/badge/laravel-%5E11.0-lime">
    <img alt="License" src="https://img.shields.io/badge/license-GPL--3.0-green"></p>

# About PizzaReader
A manga and comic reader written in Laravel and Vue.  
It is used from 2020 by many italian scanlation teams, like [lupiteam.net](https://lupiteam.net), [phoenixscans.com](https://www.phoenixscans.com) and [gtothegreatsite.net](https://reader.gtothegreatsite.net), that is the reason why it is called PizzaReader.  

It supports [Mihon](https://mihon.app/) (fork of the discontinued [Tachiyomi](https://tachiyomi.org/)), a manga reader Android application and I created a [multisrc class](https://github.com/keiyoushi/extensions-source/tree/main/lib-multisrc/pizzareader) there (originally it was in the official [tachiyomi-extensions](https://github.com/tachiyomiorg/tachiyomi-extensions/tree/master/multisrc/overrides/pizzareader) and now deleted repo) to allow you to easily create an extension and make your reader available in the most famous opensource manga reader app. Of course, other Tachiyomi forks may also be compatible. 

The admin panel is inspired by the old famous [FoOlSlide2](https://github.com/chocolatkey/FoOlSlide2) because I started to develop this reader for personal use, but other italian teams contacted me and asked to install it on their websites.  
I made this reader publicly available in May 2023.

Current available languages:
- english
- italian
- russian

To add other languages feel free to submit pull requests copying from [it.json](lang/it.json) and [it.js](resources/js/lang/it.js).  
**Tip**: edit `APP_LOCALE` in your `.env` file to change the language.


## Secondary features
You can add custom HTML (which includes js and css code) in many pages enabling the `EDIT_CUSTOM_HTML` feature in your `.env` file.  
When you finished to edit the HTML in the settings page you should disable this feature back.  
It supports (via settings) the PDF and ZIP downloads of chapters/volumes.  

## Screenshots
Click the images to open it.

<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/f893f029-0378-4711-b75d-53b757d80deb" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/280e4f24-5ac2-4e26-bf03-cbe2a082f32c" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/4810ba54-9135-42ff-a5b2-55a897d32e13" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/1973fe89-3db8-4bd3-b3bb-09d60a191bad" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/7e3541da-55b9-4729-a570-875c80c2296f" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/6358ab59-7de2-4834-964e-f9f1245dfb4e" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/40c302aa-a93e-429e-abdc-dfe96f2c4e00" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/1be6c44d-7b38-4d8b-9563-a70d840314c7" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/73fc9a77-9ab3-4814-a6e1-41169f3b8a09" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/eb4c7cfb-f624-41d0-ae68-53184b802f05" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/4492da01-c136-4e9a-9feb-d324c88d8dac" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/d06f6b0f-d1cd-4674-be60-d869a8827795" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/84151f5b-5949-4d43-96d8-21a1502340fa" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/0ef4c7c7-0db4-4e41-8548-3014d75a8e7e" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/d57dbfd3-edff-4aa5-92b2-3e081133cbe1" width="300" />
<img src="https://github.com/FedericoHeichou/PizzaReader/assets/34757141/5a4b54a1-afdb-4b46-8a5c-6132aef046dd" width="300" />

# Current version
The `master` version usually is stable because branches are merged after being tested in production on differents websites.  
If you see my last commit is old, you can consider the `master` branch stable, I just forgot to tag.  
Sometime I merge dependabot's pull requests on `master` without rebuilding the application because most of them are only for development.

The current stable version is based on Laravel 11 and requires PHP >=8.2.  
Older versions are not mainted at all.

# Installation
## Important
If you get forbidden 403 with images perform manually `cd public; ln -s ../storage/app/public storage`.  
Installing automatic cronjobs is not required, but is strongly recommended, otherwise you should run them manually from time to time.  
Why are cron jobs useful? Because they clear the database and file system of unused data.  
If after months you notice that the Reader is slowing down, you are probably not running the cronjobs.

## Settings
All configurable settings are listed in the [.env.example](.env.example) file and explained in [config/app.php](config/app.php).  
Most of them are only set during installation. After installation you can customise your reader using the settings menu in the admin dashboard.  
After editing your `.env` file you should run `php artisan config:cache`. If you don't have access to your server, you can go to the settings menu in the admin dashboard and press the "save" button: it will do a `config:cache` by itself.

## Docker
Here a dummy repository to run the reader with Docker: [PizzaReader-docker](https://github.com/FedericoHeichou/PizzaReader-docker).  
This is not production ready, but it can be useful for testing and with some modifications it can be used in production.

## Requirements
Basic requirements are listed in the [composer.json](composer.json) file.  
Other required system libraries and settings are listed in the [PizzaReader-docker's Dockerfile](https://github.com/FedericoHeichou/PizzaReader-docker/blob/main/Dockerfile).

## How to
```bash
# If you want you can specify a tag
git clone https://github.com/FedericoHeichou/PizzaReader.git pizzareader
cd pizzareader

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

cd public; ln -s ../storage/app/public storage
sed -i 's/RewriteEngine On/RewriteEngine On\n    RewriteCond %{HTTP_HOST} ^\(.+\)\\.reader\\.pizzareader\\.local$   [NC]\n    RewriteRule ^\(.*\)$ https:\/\/reader\\.pizzareader\\.local%{REQUEST_URI} [R=301,QSA,NC,L]/g' .htaccess
cd ..

php composer.phar install --no-dev
cp .env.example .env
sed -i 's/^\(APP_NAME=\).*$/\1PizzaReader/' .env
sed -i "s,^\(APP_URL=\).*$,\1https:\/\/reader.pizzareader.local," .env
sed -i "s/^\(DB_HOST=\).*$/\1pizzareader.mysql.db/" .env
sed -i "s/^\(DB_PORT=\).*$/\13306/" .env
sed -i "s/^\(DB_DATABASE=\).*$/\1pizzareader/" .env
sed -i "s/^\(DB_USERNAME=\).*$/\1pizzareader/" .env
sed -i "s/^\(DB_PASSWORD=\).*$/\1password/" .env

# If you want to change the language, you need to replace the value of "APP_LOCALE".

sed -i "s/APP_ENV=production/APP_ENV=local/" .env
# sed -i "s/APP_DEBUG=false/APP_DEBUG=true/" .env
php composer.phar dump-autoload
php artisan key:generate
php artisan migrate
php artisan db:seed
sed -i "s/APP_ENV=local/APP_ENV=production/" .env
# sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
php artisan config:cache
php composer.phar dump-autoload

find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;
# Maybe you need to chmod/chown the upload's directory
```

You are now ready to register and login at `/admin`.

## Migrating from another reader
Check in the FAQs below if you want a shortcut to import a chapter.

# Update
How to update your reader:
```bash
php artisan down --render='maintenance' --secret='YourCustomSecret' || php artisan down
# You can bypass maintenance mode by visiting your site with the secret URI you choose, for example https://pizzareader.local/YourCustomSecret
git pull origin master
php composer.phar install --no-dev
php artisan cache:clear
php artisan config:cache
php artisan migrate
php artisan up
```
# Cron
To run cronjobs add to the local crontab
```bash
* * * * * cd /path-to-your-reader && /usr/bin/php artisan schedule:run > /dev/null 2>&1
```

If you have not a local crontab you can run in a remote one
```bash
* * * * * /usr/bin/curl "https://your-reader.local/cron.php?t=$(date +%s)" 2>&1
```

## Cron config
Some cronjobs can be configured.  
For example you can set `CRON_VIEWS_CLEAR_DAYS` in your `.env` to specify the value passed with `--days` to [`views:clear`](#manually-clear-views-table) command.  
All configurable settings are listed in the [.env.example](.env.example) file.

# FAQ
Sometime I add features or bugfix who requires you to run manually certain commands for readers updated from old versions.  
You can read these FAQs to findout if you have some of problems listed here and how to solve them.

## There are orphaned ZIPs
First execute the command with `--dry-run` to emulate the clear:
```bash
php artisan downloads:clear --dry-run
```
Then if you like the output run:
```bash
php artisan downloads:clear
```

## Some comics covers are not resized
If your homepage is very heavy because your tumbnails are too big, run:
```
php artisan thumbnail:resize
```
Thumbnail with a small size will be regenerated.  
Usually there should be already resized thumbnail, but comics created in a very old version of the reader could not have it.

## Manually clear views table
If your views table is very big, it means you are not running a [cron](#cron).  
Anyway you can manually clear views table:
```bash
php artisan views:clear --days=30
```
It will remove all views older than N days (default 30).  
**Note**: it will not decrease the views counter, it only remove the combinations of `(ip, chapter)`, which means a user who read a chapter one month ago will increase the counter again in the next visit.

## Manually clear rating table
I don't suggest to perform this action if your table is not so big, but `sums` and `counts` used to calculate the average ratings are stored with the chapters too.  
The rating table has three purposes:
- preventing an IP address to vote the same chapter multiple times
- as a fallback for chapters who are voted last time in a old version of the reader (<=v1.0.1)
- as a fallback if a row of the `chapters` table is broken (for example if someone manually set `rating_sum` to 0 and users lower their rating it prevents a negative `rating_sum` and recalculate them from the `ratings` table).

If you don't care and still need to clear the table I suggest to perform a backup of `chapters` and `ratings` tables then run:
```bash
php artisan ratings:clear --days=30
```
If you need to recover a deletion, reimport the `ratings` table then set to 0 all `rating_count` of chapters you need to recalculate; the next vote will force a recalculation of sums, counts and averages of voted chapter.

## Some pages are not in the filesystem
Sometime for reasons some pages are missing in the filesystem (for example you uploaded some chapters with unstable connection without checking the result).  
This command will get all pages in the database and checks if they exists in the filesystem.  
The missing pages will be printed.  
Adding `--csv` will show the comics, chapter and page in a human readable mode with a link to the missing page instead of the only file's absolute path (you can normally open it with Excel too).
```bash
php artisan pages:check
php artisan pages:check --csv
```

## There are many files with no extension in the storage
If you are using a local cache (this is the default and most web service providers do not have caching capabilities), the reader will write its cache files to `storage/framework/cache/data`.  
In addition, if you have enabled rate limiting in the API to prevent malicious IPs from making too many requests in a few seconds, a small file will be created for each IP.  
If you notice you have thousands of files it means you are not running cronjobs or you are using an old version of the reader, in fact in the latest version a garbage collector (GC) has been added and it clears the filesystem by itself.  
You can run the garbage collector manually by running `php artisan cache:gc --progress`.

## How to import a chapter
First, the comic has to exist, so you have to to create it manually in the admin panel.  
Then you need to upload the chapter pages to a directory.  

Finally, you can run the `chapters:import` command to import the chapter, specifying the directory and comic slug or ID.  
All the images in the directory will be imported as pages of the chapter.  
You can also specify some optional parameters like the chapter title, volume, number, subchapter, visibility, license, publication date, publication start date, publication end date, timezone, language, team, team2 and views; but as in the panel you can't have duplicate chapters, so they must have a unique combination of volume, chapter and subchapter.  

**Note**: the default value of `hidden` is the same as `default_hidden_comic` in the settings (which is usually `true`), so if you want to publish the chapter immediately, you may need to set it to `false`.

Upload the chapter somewhere in your server, then run:
```bash
php artisan chapters:import /path/to/pages/dir test-comic --team=Pizzaioli --hidden=false --chapter=1 --title="Chapter test"
```

After the import, if the command was successful, you can delete the directory containing the pages because they are now copied to the storage.  

This is an example output of the `--help` command:
```bash
Description:
  Import a chapter with all its pages for a specific comic

Usage:
  chapters:import [options] [--] <pages_directory> <comic_slug_or_id>

Arguments:
  pages_directory                      Target directory containing the pages which will be imported
  comic_slug_or_id                     Comic slug or ID

Options:
      --title[=TITLE]                  Chapter title
      --volume[=VOLUME]                Chapter volume
      --chapter[=CHAPTER]              Chapter number
      --subchapter[=SUBCHAPTER]        Chapter subchapter
      --hidden[=HIDDEN]                Chapter visibility. Remember to set to false if you want to publish it immediately [default: configured default_hidden_comic value]
      --licensed[=LICENSED]            Specify if the chapter is licensed [default: "false"]
      --published_on[=PUBLISHED_ON]    Chapter publication date in the format YYYY-MM-DDTHH:MM. If not set, the current date will be used
      --publish_start[=PUBLISH_START]  Chapter publication start date in the format YYYY-MM-DDTHH:MM. If not set, published_on will be used
      --publish_end[=PUBLISH_END]      Chapter publication start date in the format YYYY-MM-DDTHH:MM. If not set, the publication will never end
      --timezone[=TIMEZONE]            Chapter timezone. If not specified, the current PHP timezone will be used. Example: Europe/Rome
      --language[=LANGUAGE]            Chapter language. If not specified, the reader default language will be used
      --team[=TEAM]                    Team slug or ID
      --team2[=TEAM2]                  Team2 slug or ID
      --views[=VIEWS]                  Chapter views [default: "0"]
  -h, --help                           Display help for the given command. When no command is given display help for the list command
  -q, --quiet                          Do not output any message
  -V, --version                        Display this application version
      --ansi|--no-ansi                 Force (or disable --no-ansi) ANSI output
  -n, --no-interaction                 Do not ask any interactive question
      --env[=ENV]                      The environment the command should run under
  -v|vv|vvv, --verbose                 Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```


# Donations
Donations are appreciated, feel free to contact me at the email listed in my profile: [FedericoHeichou](https://github.com/FedericoHeichou).
