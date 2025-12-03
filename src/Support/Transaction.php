<?php

namespace Support;

use Closure;
use Illuminate\Support\Facades\DB;

class Transaction
{
    /**
     * @throws \Throwable
     */
    public static function run(
        Closure $callback,
        Closure $finished = null,
        Closure $onError = null,
    ) {
        try {
            DB::beginTransaction();

            return tap($callback(), function ($result) use ($finished) {
                DB::commit();

                if (!is_null($finished)) {
                    $finished($result);
                }
            });
        } catch (\Throwable $e) {
            DB::rollBack();

            if (!is_null($onError)) {
                $onError($e);
            }

            throw $e;
        }
    }
}
