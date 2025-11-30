<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    /**
     * Fillable untuk mass assignment
     */
    protected $fillable = [
        'user_id',
        'activity',
        'subject_type',
        'description',
        'url',
        'date',
        'time',
        'data',
        'ip_address',
        'user_agent',
    ];

    /**
     * Cast kolom data menjadi array
     */
    protected $casts = [
        'data' => 'array',
        'date' => 'date',
        'time' => 'datetime:H:i:s',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }


    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper function untuk log activity
     */
    public static function log($activity, $description = null, $data = [], $userId = null)
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'activity' => $activity,
            'description' => $description,
            'data' => $data,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
        ]);
    }
}
