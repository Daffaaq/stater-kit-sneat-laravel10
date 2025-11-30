<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class MenuItem extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'menu_items';

    protected $fillable = [
        'name',
        'route',
        'permission_name',
        'menu_group_id',
        'order',
    ];

    /**
     * Relasi: MenuItem milik MenuGroup
     */
    public function menuGroup()
    {
        return $this->belongsTo(MenuGroup::class);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($menuItem) {
            $menuItem->logActivity('Menu Item Created', "Menu Item {$menuItem->name} dibuat", $menuItem->toArray());
        });

        static::updated(function ($menuItem) {
            $menuItem->logActivity('Menu Item Updated', "Menu Item {$menuItem->name} diperbarui", $menuItem->getChanges());
        });

        static::deleting(function ($menuItem) {
            $menuItem->logActivity('Menu Item Deleted', "Menu Item {$menuItem->name} dihapus", $menuItem->toArray());
        });
    }
}
