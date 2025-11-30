<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait ActivityLoggable
{
    /**
     * Simpan log activity
     *
     * @param string $activity
     * @param string|null $description
     * @param array $data
     * @param int|null $userId
     * @return \App\Models\ActivityLog
     */
    public function logActivity(string $activity, ?string $description = null, array $data = [], ?int $userId = null)
    {
        $dataString = !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'activity' => $activity,
            'subject_type' => get_class($this),
            'description' => $description,
            'data' => $dataString,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
        ]);
    }
}
