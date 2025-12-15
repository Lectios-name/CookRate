<table class="admin-table">
    <thead>
    <tr>
        <th>Аватар</th>
        <th>Имя</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    @forelse($users as $user)
        <tr>
            <td>
                <div class="avatar-circle avatar-small">
                    @if($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}">
                    @else
                        Ава
                    @endif
                </div>
            </td>
            <td>{{ $user->name }} {{ $user->surname }}</td>
            <td>{{ $user->login }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->is_banned) <span style="color:red;">Бан</span> @else <span style="color:green;">Актив</span> @endif
            </td>
            <td>
                @if($user->is_banned)
                    <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-success">Разбанить</button>
                    </form>
                @else
                    <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-danger">Забанить</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6">Нет пользователей</td></tr>
    @endforelse
    </tbody>
</table>
{{ $users->links() }}
