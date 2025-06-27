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
5. intervention/image - ресайз картинок

---

# Команды
- php artisan app:install - команда для установки проекта\
- php artisan app:refresh - рефреш таблиц и сидов
- php artisan queue:work - выполнение заданий

---

# Пакеты php
- debugbar
- telescope
- socialite

---

# Что сделано

## Каталог

- полнотекстовый поиск на уровне mysql
- фильтры по цене и брендам
- ресайз картинок
- категории, товары, бренды, цены, меню

## Модели со связями
- belongsTo
- belongsToMany
- hasMany
---
## ларавель framework
- Миграции
- Фабрики
- Сид бд в том числе с картинками
- изменены стабы миграций
- компоненты
- реквесты
- контроллеры
- события
- провайдеры
- тесты
- модели
  
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
## модели 

- трейты модели
  - HasSlug логика формарования слагов модели
  - HasThumbnail логика ресайза картинок модели
  - Cacheable кеш (сброс по событиям create update delete модели)
- фабрики моделей
- кастомные query builder
- кастомные collections

---

## реквесты
фабики реквестов

---

## личный кабинет
- авторизация
- регистрация
- восстановление пароля
- авторизация через github
---

## фронт
- vite
- tailwindcss
- alpinejs
---

## прочее
- стилизованные flash уведомления для пользователей
- логгер Telegram
- тестирование Socialite авторизации и TelegramBotApi логирования через моковые классы
---

# Концепции
- DDD
  - Actions - экшены домена
  - Contracts - контракты
  - DTOs - объекты дто
  - Providers - провайдеры домена
  - Models - модели домена
  - Collections - дополненные коллекции моделей
  - QueryBuilders - дополненые query builder модели
  - ViewModels - вынесение запросов данных для отображения из контроллеров в классы представлния
---

# CI
при pull request или push в ветку master происходит запуск тестов и проверка форматирования кода на github файл .github/workflows/laravel.yml

---

# PSR-12
для автоформатирования кода есть команда `composer lint:fix`
