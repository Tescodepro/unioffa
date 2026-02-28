<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePermission extends Model
{
    protected $primaryKey = 'route_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'route_name',
        'permission_identifier',
    ];

    /**
     * Look up the required permission for a given route name.
     * Returns null if the route has no permission requirement.
     */
    public static function requiredFor(string $routeName): ?string
    {
        return static::where('route_name', $routeName)->value('permission_identifier');
    }
}
