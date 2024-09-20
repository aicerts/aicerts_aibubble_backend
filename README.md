
# aicerts_aibubble_backend

AI Bubble backend


## Tech Stack

**Client:** HTML, CSS, JavaScript, jQuery, VueJs, Bootstrap 4

**Server:** PHP, Laravel 8

**DataBase:** MySql


## Installation

Install All Packages of laravel
```bash
composer install
```

Create .env file
```bash
cp .env.example .env
```

Add MarketStack API Key in .env
```
MARKET_API_KEY=YOUR_API_KEY_HERE
```

Generate Application key

```bash
php artisan key:generate
```

Link Storage
```bash
php artisan storage:link
```

Update .env File with Database credentials and run migration with seed.
```bash
php artisan migrate --seed
```

All Set ! now serve laravel app on local and open app in browser.

Login With Admin
```bash
Username - admin@admin.com
Password - 12345678
```

## Cron Job
Setup following cron job to auto update symbols prices in background

#### Add below job in crontab to run every 5 mins
```bash
*/5 * * * * php artisan schedule:run 1>> /dev/null 2>&1
```