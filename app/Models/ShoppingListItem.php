<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = ['shopping_list_id', 'ingredient_name', 'quantity', 'is_completed'];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }
}
