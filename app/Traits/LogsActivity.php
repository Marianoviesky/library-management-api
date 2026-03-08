<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            Log::info(class_basename($model) . " #{$model->id} was created");
        });

        static::updated(function ($model) {
            Log::info(class_basename($model) . " #{$model->id} was updated");
        });

        static::deleted(function ($model) {
            Log::info(class_basename($model) . " #{$model->id} was deleted");
        });
    }
}
