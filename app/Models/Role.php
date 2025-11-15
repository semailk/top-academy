<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property bool $status
 */
class Role extends Model
{
    protected $fillable = ['name', 'status'];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
