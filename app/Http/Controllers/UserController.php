<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Http\Requests\AutorizationRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function auth_post(AutorizationRequest $request)
    {
        $credentials = $request->validated();

        // Проверяем, существует ли пользователь и не забанен ли он
        $user = User::where('login', $credentials['login'])->first();

        if ($user && $user->is_banned) {
            return back()->withErrors(['login' => 'Ваш аккаунт забанен администратором.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('main');
        }

        return back()->withErrors(['login' => 'Неверные данные для входа.']);
    }

    public function register_post(RegistrationRequest $request)
    {
        $requests = $request->validated();
        $requests['password'] = Hash::make($requests['password']);
        User::create($requests);
        return redirect()->route('auth');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerate();
        return redirect()->route('main');
    }

    public function personal()
    {
        $user = Auth::user();
        $recipes = Recipe::where('user_id', $user->id)->with('ratings')->latest()->paginate(6);
        $favorites = $user->favorites()->with('ratings')->latest()->paginate(6);

        return view('profile.personal', compact('user', 'recipes', 'favorites'));
    }

    public function updatePersonal(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $data = $request->only(['name', 'surname', 'email']);

        if ($request->filled('bio')) {
            $data['bio'] = $request->bio;
        }

        // Загрузка аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар из public/avatars/
            if ($user->avatar_path && file_exists(public_path($user->avatar_path))) {
                unlink(public_path($user->avatar_path));
            }

            // Сохраняем в public/avatars/
            $avatar = $request->file('avatar');
            $filename = 'avatars/' . time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('avatars'), $filename);
            $data['avatar_path'] = $filename;
        }

        $user->update($data);

        Auth::setUser($user->fresh());

        return back()->with('success', 'Профиль обновлён!');
    }

    public function removeAvatar()
    {
        $user = Auth::user();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }
        return back();
    }
}

