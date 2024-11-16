<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
        /**
         * если запрос в бд слишком долго обрабатывается отправляем лог в телеграм
         */
        DB::whenQueryingForLongerThan(500, function (Connection $connection) {
            logger()
                ->channel('telegram')
                ->debug('whenQueryingForLongerThan:' . $connection->query()->toSql());
        });
        /**
         * если запрос слишком долго отрабатывает, то отправляем лог в телеграмл
         */
        $kernel = app(Kernel::class);
        $kernel->whenRequestLifecycleIsLongerThan(
            CarbonInterval::seconds(4),
            function () {
                logger()
                    ->channel('telegram')
                    ->debug('whenRequestLifecycleIsLongerThan:' . request()->url());
            }
        );
    }
}
