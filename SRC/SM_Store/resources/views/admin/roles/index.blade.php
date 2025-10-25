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

    <!-- Seller Requests Section -->
    @if(count($sellerRequests) > 0)
    <div class="mb-8">
        <h3 class="orbitron text-xl font-bold text-white mb-4">📝 Yêu cầu trở thành Seller</h3>
        <div class="bg-white rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lý do</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Kinh nghiệm</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Portfolio</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ngày gửi</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sellerRequests as $request)
                        <tr class="border-b last:border-none hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $request['email'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm max-w-xs">
                                <div class="truncate" title="{{ $request['reason'] ?? 'N/A' }}">
                                    {{ Str::limit($request['reason'] ?? 'N/A', 50) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($request['experience'] == 'beginner') bg-green-100 text-green-800
                                    @elseif($request['experience'] == 'intermediate') bg-yellow-100 text-yellow-800
                                    @elseif($request['experience'] == 'advanced') bg-orange-100 text-orange-800
                                    @elseif($request['experience'] == 'professional') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @switch($request['experience'])
                                        @case('beginner') Mới bắt đầu @break
                                        @case('intermediate') Trung bình @break
                                        @case('advanced') Nâng cao @break
                                        @case('professional') Chuyên nghiệp @break
                                        @default {{ $request['experience'] ?? 'N/A' }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($request['portfolio'])
                                    <a href="{{ $request['portfolio'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                        Xem Portfolio
                                    </a>
                                @else
                                    <span class="text-gray-400">Không có</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ isset($request['created_at']) ? \Carbon\Carbon::parse($request['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.seller-requests.approve', $request['id']) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs"
                                                onclick="return confirm('Bạn có chắc chắn muốn chấp nhận yêu cầu này?')">
                                            ✅ Chấp nhận
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.seller-requests.reject', $request['id']) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs"
                                                onclick="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu này?')">
                                            ❌ Từ chối
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Users Management Section -->
    <div class="mb-4">
        <h3 class="orbitron text-xl font-bold text-white mb-4">👥 Quản lý người dùng</h3>
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
