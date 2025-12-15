<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'login',
        'email',
        'password',
        'bio',
        'avatar_path',
        'isAdmin',
        'is_banned',
    ];

    public function favorites()
    {
        return $this->belongsToMany(Recipe::class, 'favorites');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            // Если путь начинается с 'avatars/', значит — из public/
            if (str_starts_with($this->avatar_path, 'avatars/')) {
                return asset($this->avatar_path);
            }
            // Старый путь из storage
            return asset('storage/' . $this->avatar_path);
        }
        return null;
    }
    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }
}
