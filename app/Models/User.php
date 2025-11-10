<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $status
 * @property string $avatar
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'avatar'
    ];

    protected $appends = [
        'avatar_url'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAdmin(): bool
    {
        return $this->status === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        return isset($this->attributes['avatar']) && $this->attributes['avatar']
                ? \url($this->attributes['avatar']) : url('images.png');
    }

    public function setAvatarAttribute(?string $value): void
    {
        $this->attributes['avatar'] = $value ?? public_path('images.png');
    }

    // Добавляем метод для совместимости с Laravel Auth
    // public function getAuthIdentifierName()
    // {
    //     return 'username';
    // }
}
