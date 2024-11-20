# Установка
- копировать .env.example и назвать .env -> установить актуальные настройки
- в .env FILESYSTEM_DISK установить public при необходимости
- php artisan app:install
- php artisan key:generate при необходимости или указать ключ вручную
- указать LOG_STACK и LOG_LEVEL
> LOG_STACK поддерживает также канал telegram

---

# Зависимости
1. barryvdh/laravel-debugbar - отладка
> работает когда в .env APP_DEBUG=true и APP_ENV не production
2. laravel/telescope - просмотр логов

---

# Что сделано
## Разработка
InstallCommand - команда для установки проекта

---
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
Миграции

---
Фабрики

---
Сид бд

---
изменены стабы миграций

---
логика формароватьия слагов модели
