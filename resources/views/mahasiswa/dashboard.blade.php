<x-layouts.app title="Dashboard KALAB">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-4 gap-5 mb-8">
        @php
            $stats = [
                ['label' => 'Total Mahasiswa', 'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->count(), 'icon' => '🎓', 'color' => 'bg-sky-50 border-sky-100',    'text' => 'text-sky-600'],
                ['label' => 'Total ASLAB',     'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','aslab'))->where('is_active',true)->count(),     'icon' => '🧪', 'color' => 'bg-emerald-50 border-emerald-100','text' => 'text-emerald-600'],
                ['label' => 'Total Dosen',     'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','dosen-pembimbing'))->where('is_active',true)->count(), 'icon' => '💼', 'color' => 'bg-amber-50 border-amber-100',  'text' => 'text-amber-600'],
                ['label' => 'User Aktif',      'value' => \App\Models\User::where('is_active',true)->count(), 'icon' => '✅', 'color' => 'bg-violet-50 border-violet-100','text' => 'text-violet-600'],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="bg-white rounded-2xl border {{ $stat['color'] }} p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $stat['color'] }} flex items-center justify-center text-2xl flex-shrink-0">
                    {{ $stat['icon'] }}
                </div>
                <div>
                    <p class="text-2xl font-display font-bold {{ $stat['text'] }}">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Grafik user per role --}}
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-display font-bold text-gray-900">Distribusi Pengguna</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Berdasarkan role aktif</p>
                </div>
            </div>
            @php
                $roleStats = \App\Models\Role::withCount(['users' => fn($q) => $q->where('is_active', true)])->get();
                $maxCount  = $roleStats->max('users_count') ?: 1;
                $roleColors = ['kalab' => '#7c3aed','mahasiswa' => '#0ea5e9','aslab' => '#10b981','dosen-pembimbing' => '#f59e0b'];
            @endphp
            <div class="space-y-4">
                @foreach ($roleStats as $r)
                    <div class="flex items-center gap-4">
                        <p class="text-sm text-gray-600 w-36 truncate flex-shrink-0">{{ $r->name }}</p>
                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-700"
                                 style="width: {{ $maxCount > 0 ? ($r->users_count / $maxCount * 100) : 0 }}%; background-color: {{ $roleColors[$r->slug] ?? '#6b7280' }}">
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 w-6 text-right">{{ $r->users_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Aktivitas terakhir --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-display font-bold text-gray-900 mb-4">Pengguna Terbaru</h3>
            @php
                $latestUsers = \App\Models\User::with('role')->latest()->take(6)->get();
            @endphp
            <div class="space-y-3">
                @forelse ($latestUsers as $u)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             style="background-color: {{ $roleColors[$u->role?->slug ?? ''] ?? '#6b7280' }}">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400">{{ $u->role?->name }}</p>
                        </div>
                        <span class="text-[10px] text-gray-300">{{ $u->created_at->diffForHumans(null, true) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Tabel semua user --}}
        <div class="col-span-3 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-display font-bold text-gray-900">Daftar Pengguna</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Semua akun terdaftar</p>
                </div>
            </div>
            @php
                $users = \App\Models\User::with('role')->latest()->take(10)->get();
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Nama</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Username</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Email</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Role</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Status</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($users as $u)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 font-medium text-gray-800">{{ $u->name }}</td>
                                <td class="py-3 text-gray-500 font-mono text-xs">{{ $u->username }}</td>
                                <td class="py-3 text-gray-500">{{ $u->email }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: {{ $roleColors[$u->role?->slug ?? ''] ?? '#f3f4f6' }}22; color: {{ $roleColors[$u->role?->slug ?? ''] ?? '#6b7280' }}">
                                        {{ $u->role?->name }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($u->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-50 text-red-500 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-gray-400 text-xs">{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
