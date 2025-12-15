<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'name', 'description', 'image_path', 'time_minutes'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function ingredients() {
        return $this->hasMany(Ingredient::class);
    }

    public function steps() {
        return $this->hasMany(RecipeStep::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        $minutes = $this->time_minutes;

        if (!$minutes || $minutes <= 0) {
            return '';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' ч';
        }

        if ($remainingMinutes > 0) {
            $parts[] = $remainingMinutes . ' мин';
        }

        return implode(' ', $parts);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // Динамический атрибут
    public function getFavoritesCountAttribute()
    {
        return $this->favoritedBy()->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Средний рейтинг (1-5)
    public function getAverageRatingAttribute()
    {
        // Используем cash:ratings для оптимизации
        return $this->ratings()->avg('value') ?: 0;
    }

    // Количество оценок
    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }
}
