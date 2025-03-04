<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsModelChanges
{
    public static function bootLogsModelChanges()
    {
        static::created(function ($model) {
            Log::channel('model')->info("Created: " . get_class($model), [
                'id' => $model->id,
                'data' => $model->toArray(),
                'causer' => Auth::user() ? [
                    'name' => Auth::user()->name,
                ] : null,
            ]);
        });

        static::updated(function ($model) {
            Log::channel('model')->info("Updated: " . get_class($model), [
                'id' => $model->id,
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
                'causer' => Auth::user() ? [
                    'name' => Auth::user()->name,
                ] : null,
            ]);
        });

        static::deleted(function ($model) {
            Log::channel('model')->info("Deleted: " . get_class($model), [
                'id' => $model->id,
                'data' => $model->toArray(),
                'causer' => Auth::user() ? [
                    'name' => Auth::user()->name,
                ] : null,
            ]);
        });
    }
}

