<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

use Illuminate\Support\Facades\Auth;


class ShoppingListController extends Controller
{
    public function index()
    {
        $lists = Auth::user()->shoppingLists()->with('items')->get();
        return response()->json($lists);
    }

    // Создать список
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $list = Auth::user()->shoppingLists()->create($request->only('name'));
        return response()->json($list);
    }

    // Добавить ингредиенты в список
    public function addItems(Request $request)
    {
        $request->validate([
            'list_id' => 'required|exists:shopping_lists,id',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string',
            'ingredients.*.quantity' => 'required|string',
        ]);

        $list = ShoppingList::findOrFail($request->list_id);
        if ($list->user_id !== Auth::id()) {
            abort(403);
        }

        foreach ($request->ingredients as $ing) {
            $list->items()->create([
                'ingredient_name' => $ing['name'],
                'quantity' => $ing['quantity'],
            ]);
        }

        return response()->json(['message' => 'Ингредиенты добавлены']);
    }

    // Обновить статус ингредиента (галочка)
    public function toggleItem(ShoppingListItem $item)
    {
        if (!$item) {
            abort(404, 'Элемент не найден');
        }

        if ($item->shoppingList->user_id !== Auth::id()) {
            abort(403);
        }

        $item->update(['is_completed' => !$item->is_completed]);

        return response()->json($item);
    }

    // Удалить список
    public function destroy(ShoppingList $shoppingList)
    {
        if ($shoppingList->user_id !== Auth::id()) {
            abort(403);
        }
        $shoppingList->delete();
        return response()->json(['message' => 'Список удалён']);
    }


}
