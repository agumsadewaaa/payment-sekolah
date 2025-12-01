<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Generic helper to create an activity log.
     *
     * @param string $action
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param string|null $modelType
     * @param mixed $modelId
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string|null $description
     * @return ActivityLog
     */
    public static function log(string $action, $user = null, ?string $modelType = null, $modelId = null, ?array $oldValues = null, ?array $newValues = null, ?string $description = null)
    {
        $userId = $user ? ($user->id ?? null) : (Auth::check() ? Auth::id() : null);

        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId ? (string) $modelId : null,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'description' => $description,
        ]);
    }
}
