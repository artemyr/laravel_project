# Установка
- копировать .env.example и назвать .env -> установить актуальные настройки, также сделать .env.testing для тестов
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
3. worksome/request-factories - генератор запросов
4. laravel/socialite - соцсети

---

# Команды
- php artisan app:install - команда для установки проекта\
- php artisan app:refresh - рефреш таблиц и сидов
- php artisan queue:work - выполнение заданий

# Что сделано
Пакеты php
- debugbar
- telescope
---
Модели со связями
- belongsTo
- belongsToMany
- hasMany
---
ларавель framework
- Миграции
- Фабрики
- Сид бд в том числе с картинками
- изменены стабы миграций
- логика формарования слагов модели
- компоненты
- реквесты
- контроллеры
- события
- провайдеры
- тесты
  
AppServiceProvider
- Конфигурация настроек моделей
- Логгирование в телеграм в случае долгой обработки запроса бд
- Логгирование в телеграм в случае долгой обработки скрипта php

RouteServiceProvider
- RateLimit - лимиты на количество запросов

ViewServiceProvider
- доп команда Vite::image() для фасада через macro

bootstrap/app
- кастомная обработка исключений

---
личный кабинет
- авторизация
- регистрация
- восстановление пароля
- авторизация через github
---
фронт
- vite
- tailwindcss
- alpinejs
---
прочее
- стилизованные flash уведомления для пользователей
- логгер Telegram
---
