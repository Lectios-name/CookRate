<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
    protected $fillable = ['recipe_id', 'step_number', 'description', 'photo_path'];
}
