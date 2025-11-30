<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class MenuGroup extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'menu_groups';

    protected $fillable = [
        'name',
        'permission_name',
        'icon',
        'route',
        'order',
    ];

    /**
     * Relasi: MenuGroup punya banyak MenuItem
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * Boot method to handle model events
     */

    protected static function boot()
    {
        parent::boot();

        static::created(function ($menuGroup) {
            $menuGroup->logActivity('Menu Group Created', "Menu Group {$menuGroup->name} dibuat", $menuGroup->toArray());
        });

        static::updated(function ($menuGroup) {
            $menuGroup->logActivity('Menu Group Updated', "Menu Group {$menuGroup->name} diperbarui", $menuGroup->getChanges());
        });

        static::deleting(function ($menuGroup) {
            $menuGroup->logActivity('Menu Group Deleted', "Menu Group {$menuGroup->name} dihapus", $menuGroup->toArray());
        });
    }
}
