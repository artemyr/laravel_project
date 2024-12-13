# Установка
- копировать .env.example и назвать .env -> установить актуальные настройки
- в .env FILESYSTEM_DISK установить public при необходимости
- настроить соединение с бд
- установить зависимости composer
- php artisan app:install
- php artisan key:generate при необходимости или указать ключ вручную
- указать LOG_STACK и LOG_LEVEL
> LOG_STACK поддерживает также канал telegram\
> для telegram нужно указать TELEGRAM_CHAT_ID и TELEGRAM_BOT_TOKEN

---

# Зависимости
1. barryvdh/laravel-debugbar - отладка
> работает когда в .env APP_DEBUG=true и APP_ENV не production
2. laravel/telescope - просмотр логов

---

# Команды
InstallCommand - команда для установки проекта\
RefreshCommand - рефреш таблиц и сидов

# Что сделано
AppServiceProvider
- Конфигурация настроек моделей
- Логгирование в телеграм в случае долгой обработки запроса бд
- Логгирование в телеграм в случае долгой обработки скрипта php
---
Пакеты php
- debugbar
- telescope
---
Модели со связями
- belongsTo
- belongsToMany
- hasMany
---
- Миграции
- Фабрики
- Сид бд в том числе с картинками
- изменены стабы миграций
- логика формарования слагов модели
- компоненты
- авторизация
- реквесты
- контроллеры
---
vite
tailwindcss
---
