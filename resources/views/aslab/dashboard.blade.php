<x-layouts.app title="Dashboard ASLAB">

    {{-- Greeting --}}
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 mb-6 text-white">
        <p class="text-emerald-100 text-sm mb-1">Halo, Asisten Laboratorium 🧪</p>
        <h2 class="font-display text-2xl font-bold">{{ auth()->user()->name }}</h2>
        <p class="text-emerald-100 text-sm mt-1">ID: {{ auth()->user()->username }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-5 mb-6">
        @php
            $aStats = [
                ['label' => 'Total Mahasiswa', 'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->count(), 'icon' => '🎓', 'color' => 'bg-sky-50 border-sky-100',    'text' => 'text-sky-600'],
                ['label' => 'Kehadiran Hari Ini','value' => '—', 'icon' => '✅', 'color' => 'bg-emerald-50 border-emerald-100','text' => 'text-emerald-600'],
                ['label' => 'Penilaian Pending', 'value' => '—', 'icon' => '⏳', 'color' => 'bg-amber-50 border-amber-100',  'text' => 'text-amber-600'],
            ];
        @endphp
        @foreach ($aStats as $s)
            <div class="bg-white rounded-2xl border {{ $s['color'] }} p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $s['color'] }} flex items-center justify-center text-2xl">{{ $s['icon'] }}</div>
                <div>
                    <p class="text-2xl font-display font-bold {{ $s['text'] }}">{{ $s['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-5">

        {{-- Daftar mahasiswa --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-display font-bold text-gray-900 mb-4">Daftar Mahasiswa</h3>
            @php $mahasiswas = \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->latest()->take(6)->get(); @endphp
            <div class="space-y-3">
                @forelse ($mahasiswas as $m)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $m->name }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $m->username }}</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Belum ada mahasiswa.</p>
                @endforelse
            </div>
        </div>

        {{-- Aktivitas --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-display font-bold text-gray-900 mb-4">Aktivitas Terakhir</h3>
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="text-4xl mb-3">🕐</span>
                <p class="text-sm text-gray-400">Belum ada aktivitas tercatat.</p>
            </div>
        </div>

        {{-- Tabel penilaian --}}
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-display font-bold text-gray-900">Penilaian Terbaru</h3>
                <a href="{{ route('aslab.grading') }}" class="text-xs text-emerald-500 hover:underline font-medium">Lihat semua →</a>
            </div>
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="text-4xl mb-3">📋</span>
                <p class="text-sm text-gray-400">Belum ada data penilaian.</p>
            </div>
        </div>

    </div>
</x-layouts.app>
