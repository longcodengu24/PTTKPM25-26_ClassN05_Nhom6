{{-- resources/views/admin/roles/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Phân quyền người dùng')

@section('content')
<div class="admin-card m-6 rounded-xl p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">🛡️ Phân quyền người dùng</h2>
        <span class="text-sm text-gray-300">
            Đang đăng nhập: {{ session('email') }} (role: {{ session('role') }})
        </span>
    </div>

    @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4">
        <input id="searchEmail" type="text" placeholder="Tìm email..."
               class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30">
    </div>

    <div class="overflow-x-auto bg-white rounded-xl">
        <table class="min-w-full" id="userTable">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">UID</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Hành động</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr class="border-b last:border-none">
                    <td class="px-4 py-3 text-sm">{{ $u['uid'] }}</td>
                    <td class="px-4 py-3">{{ $u['email'] }}</td>
                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs
                            @if($u['role']=='admin') bg-red-500 text-white
                            @elseif($u['role']=='saler') bg-blue-500 text-white
                            @else bg-green-500 text-white @endif">
                            {{ ucfirst($u['role']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.roles.update', $u['uid']) }}" class="flex items-center gap-2">
                            @csrf
                            <select name="role" class="p-2 border rounded-lg">
                                <option value="user"     @selected($u['role']=='user')>User</option>
                                <option value="saler" @selected($u['role']=='saler')>Saler</option>
                                <option value="admin"    @selected($u['role']=='admin')>Admin</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                Cập nhật
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Chưa có người dùng.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchEmail')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(tr => {
        const email = tr.children[1]?.innerText?.toLowerCase() ?? '';
        tr.style.display = email.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
