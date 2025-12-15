<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function add(Recipe $recipe): RedirectResponse
    {
        auth()->user()->favorites()->attach($recipe->id);
        return back();
    }

    public function remove(Recipe $recipe): RedirectResponse
    {
        auth()->user()->favorites()->detach($recipe->id);
        return back();
    }
}
