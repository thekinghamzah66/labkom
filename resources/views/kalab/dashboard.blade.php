<x-layouts.app title="Dashboard KALAB">

        {{-- Welcome Header dengan Logo --}}
    <div class="relative overflow-hidden rounded-3xl bg-white border border-gray-100 p-8 mb-8 shadow-sm">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                {{-- Logo Kampus Besar --}}
                <div class="w-20 h-20 bg-violet-50 rounded-2xl flex items-center justify-center p-3 border border-violet-100">
                    <img src="{{ asset('img/logo-untag.jpg') }}" alt="Logo Untag" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-2xl font-display font-bold text-gray-900">Selamat Datang, Kepala Lab 👋</h1>
                    <p class="text-gray-500 mt-1">Sistem Informasi Laboratorium Komputer - Teknik Informatika</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="px-3 py-1 bg-violet-600 text-white text-[10px] font-bold rounded-full uppercase tracking-widest">Administrator</span>
                        <span class="text-xs text-gray-400">Universitas 17 Agustus 1945 Surabaya</span>
                    </div>
                </div>
            </div>
            
            {{-- Dekorasi tambahan (opsional) --}}
{{-- Dekorasi tambahan (Menggunakan Inline SVG agar pasti muncul) --}}
<div class="hidden lg:block">
    <svg class="w-32 h-32 text-violet-200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="currentColor" d="M44.7,-76.4C58.2,-69.2,70,-58.5,77.4,-45.5C84.8,-32.5,87.8,-17.2,86.5,-2.4C85.2,12.5,79.6,26.8,71.4,39.3C63.2,51.8,52.3,62.5,39.7,70.1C27.2,77.7,13.6,82.1,-0.8,83.5C-15.2,84.9,-30.5,83.2,-43.8,76.1C-57.1,69,-68.5,56.5,-76.3,42.4C-84.1,28.3,-88.3,12.7,-87.3,-2.6C-86.3,-17.9,-80.1,-32.8,-70.7,-45.6C-61.3,-58.4,-48.7,-69.1,-35.1,-76.1C-21.5,-83.1,-10.7,-86.4,2.9,-91.4C16.5,-96.4,31.2,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
        <g transform="translate(60, 60) scale(1.2)">
            <rect x="20" y="40" width="10" height="30" rx="2" fill="#7c3aed" />
            <rect x="40" y="20" width="10" height="50" rx="2" fill="#a78bfa" />
            <rect x="60" y="50" width="10" height="20" rx="2" fill="#c4b5fd" />
            <circle cx="45" cy="10" r="5" fill="#7c3aed" />
        </g>
    </svg>
</div>
        </div>

        {{-- Pattern Background --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-40 h-40 bg-violet-50 rounded-full blur-3xl opacity-50"></div>
    </div>

    {{-- Stats Cards --}}
    {{-- Perubahan: grid-cols-1 (HP), md:grid-cols-2 (Tablet), lg:grid-cols-4 (Desktop) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
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

    {{-- Grid Tengah: Grafik & Aktivitas --}}
    {{-- Perubahan: grid-cols-1 (HP), lg:grid-cols-3 (Desktop) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Grafik user per role --}}
        {{-- Perubahan: lg:col-span-2 agar tetap lebar di desktop --}}
{{-- Grafik Analytics Kelulusan --}}
<div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-display font-bold text-gray-900">Statistik Kelulusan Per Periode</h3>
            <p class="text-xs text-gray-400 mt-0.5">Grafik kelulusan mahasiswa antar angkatan</p>
        </div>
    </div>
    
    <div class="space-y-6">
        @foreach ($analytics as $data)
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-medium">
                    <span class="text-gray-600">{{ $data['periode'] }}</span>
                    <span class="text-emerald-600">{{ $data['lulus'] }} Lulus / <span class="text-red-400">{{ $data['gagal'] }} Gagal</span></span>
                </div>
                <div class="flex h-4 w-full bg-gray-100 rounded-full overflow-hidden">
                    {{-- Bar Lulus --}}
                    <div class="bg-emerald-500 transition-all duration-1000" style="width: {{ ($data['lulus'] / ($data['lulus'] + $data['gagal'])) * 100 }}%"></div>
                    {{-- Bar Gagal --}}
                    <div class="bg-red-400 transition-all duration-1000" style="width: {{ ($data['gagal'] / ($data['lulus'] + $data['gagal'])) * 100 }}%"></div>
                </div>
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
                        <span class="text-[10px] text-gray-300 flex-shrink-0">{{ $u->created_at->diffForHumans(null, true) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Tabel semua user --}}
        {{-- Perubahan: lg:col-span-3 agar penuh di desktop --}}
        <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-6 mt-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-display font-bold text-gray-900">Daftar Pengguna</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Semua akun terdaftar</p>
                </div>
            </div>
            @php
                $users = \App\Models\User::with('role')->latest()->take(10)->get();
            @endphp
            
            {{-- Wrapper overflow-x-auto sangat penting untuk tabel di mobile --}}
            <div class="overflow-x-auto -mx-6 px-6">
                <table class="w-full text-sm min-w-[800px]"> {{-- min-w memastikan tabel tidak gepeng di layar kecil --}}
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
                                <td class="py-3 font-medium text-gray-800 whitespace-nowrap">{{ $u->name }}</td>
                                <td class="py-3 text-gray-500 font-mono text-xs whitespace-nowrap">{{ $u->username }}</td>
                                <td class="py-3 text-gray-500 whitespace-nowrap">{{ $u->email }}</td>
                                <td class="py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: {{ $roleColors[$u->role?->slug ?? ''] ?? '#f3f4f6' }}22; color: {{ $roleColors[$u->role?->slug ?? ''] ?? '#6b7280' }}">
                                        {{ $u->role?->name }}
                                    </span>
                                </td>
                                <td class="py-3 whitespace-nowrap">
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
                                <td class="py-3 text-gray-400 text-xs whitespace-nowrap">{{ $u->created_at->format('d M Y') }}</td>
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