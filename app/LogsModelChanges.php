<?php

namespace App;

use Illuminate\Support\Facades\Log;

trait LogsModelChanges
{
    public static function bootLogsModelChanges()
    {
        static::created(function ($model) {
            Log::channel('model')->info('Created: ' . get_class($model), [
                'id' => $model->id,
                'data' => $model->toArray(),
                'causer' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->getRoleNames()->first(),
                ] : null,
            ]);
        });

        static::updated(function ($model) {
            Log::channel('model')->info('Updated: ' . get_class($model), [
                'id' => $model->id,
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
                'causer' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->getRoleNames()->first(),
                ] : null,
            ]);
        });

        static::deleted(function ($model) {
            Log::channel('model')->info('Deleted: ' . get_class($model), [
                'id' => $model->id,
                'data' => $model->toArray(),
                'causer' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->getRoleNames()->first(),
                ] : null,
            ]);
        });
    }
}
