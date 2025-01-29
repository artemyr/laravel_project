<?php

namespace App\Providers;

use App\Listeners\SendEmailNewUserListener;
use Carbon\CarbonInterval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Services\Telegram\TelegramBotApi;
use Services\Telegram\TelegramBotApiContract;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());
        /**
         * защита от проблемы ленивой загрузки отношений N+1
         * когда отношения модели подгружаются автоматически без явного указания
         * отножения нужно будет указывать явно, например так Post::with('author')->get()
         */
        Model::preventLazyLoading(!app()->isProduction());
        /**
         * в локальной разработке выдавать ошибку, если пытаемся
         * записать данные в защищенное поле модели
         */
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        $this->app->bind(TelegramBotApiContract::class, TelegramBotApi::class);

        if (app()->isProduction()) {

            /**
             * если запрос в бд слишком долго обрабатывается отправляем лог в телеграм
             */
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    logger()
                        ->channel('telegram')
                        ->debug('query longer then 1ms:' . $query->toSql());
                }
            });

            /**
             * если запрос слишком долго отрабатывает, то отправляем лог в телеграмл
             */
            app(Kernel::class)->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    logger()
                        ->channel('telegram')
                        ->debug('whenRequestLifecycleIsLongerThan:' . request()->url());
                }
            );
        }

        Event::listen(
            Registered::class,
            SendEmailNewUserListener::class
        );
    }
}
