<?php
// app/Models/User.php

namespace App\Models;

// Все необходимые импорты
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Убедимся что Post модель доступна
    public function posts()
    {
        return $this->hasMany(\App\Models\Post::class);
    }

    public function isAdmin()
    {
        return $this->status === 'admin';
    }

    public function getAvatarUrl()
    {
        if ($this->avatar) {
            $filePath = 'public/avatars/' . $this->avatar;

            if (Storage::exists($filePath)) {
                // Используем прямой роут вместо симлинка
                return route('avatar.direct', ['filename' => $this->avatar]);
            }
        }

        return asset('images/default-avatar.png');
    }
}
