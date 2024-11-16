# Установка
- php artisan key:generate
- php artisan migrate
- php artisan storage:link

в .env FILESYSTEM_DISK установить public при необходимости

---

# Зависимости
1. barryvdh/laravel-debugbar - отладка
> работает когда в .env APP_DEBUG=true и APP_ENV не production
2. laravel/telescope - просмотр логов

---

# Что сделано
## Разработка
AppServiceProvider
- Конфигурация настроек моделей
- Логгирование в телеграм в случае долгой обработки запроса бд
- Логгирование в телеграм в случае долгой обработки скрипта php

Пакеты php
- debugbar
- telescope
