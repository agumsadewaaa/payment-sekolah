<?php

namespace App\Observers;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    public function created($model)
    {
        $user = Auth::user();
        if (! $user || ! method_exists($user, 'hasRole') || ! ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return;
        }

        $start = microtime(true);

        $modelType = get_class($model);
        $modelId = $model->getKey();

        $description = sprintf("%s created %s id=%s", $user->name ?? "user:$user->id", $modelType, $modelId);

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        ActivityLogger::log('created', $user, $modelType, $modelId, null, $model->toArray(), $description, $durationMs);
    }

    public function updated($model)
    {
        $user = Auth::user();
        if (! $user || ! method_exists($user, 'hasRole') || ! ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return;
        }

        $start = microtime(true);

        $modelType = get_class($model);
        $modelId = $model->getKey();

        $old = $model->getOriginal();
        $changes = $model->getChanges();

        $description = sprintf("%s updated %s id=%s — changed fields: %s", $user->name ?? "user:$user->id", $modelType, $modelId, implode(', ', array_keys($changes)));

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        ActivityLogger::log('updated', $user, $modelType, $modelId, $old, $model->toArray(), $description, $durationMs);
    }

    public function deleted($model)
    {
        $user = Auth::user();
        if (! $user || ! method_exists($user, 'hasRole') || ! ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return;
        }

        $start = microtime(true);

        $modelType = get_class($model);
        $modelId = $model->getKey();

        $description = sprintf("%s deleted %s id=%s", $user->name ?? "user:$user->id", $modelType, $modelId);

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        ActivityLogger::log('deleted', $user, $modelType, $modelId, $model->toArray(), null, $description, $durationMs);
    }
}
