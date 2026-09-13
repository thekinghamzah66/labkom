<x-layouts.app title="Dashboard Dosen Pembimbing">

    {{-- 1. Greeting Card dengan Logo Untag --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 rounded-[2rem] p-8 mb-8 shadow-lg shadow-amber-100">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                {{-- Box Logo Kampus --}}
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center p-3 shadow-inner">
                    <img src="{{ asset('img/logo-untag.jpg') }}" alt="Logo Untag" class="w-full h-full object-contain">
                </div>
                
                <div>
                    <p class="text-amber-100 text-xs font-black uppercase tracking-[0.2em] mb-1">Quality Control & Supervisor</p>
                    <h2 class="font-display text-3xl font-black text-white leading-tight">Selamat Datang, Dosen {{ auth()->user()->name }}</h2>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold rounded-full border border-white/20">NIP: {{ auth()->user()->username }}</span>
                        <span class="px-3 py-1 bg-amber-400 text-amber-900 text-[10px] font-bold rounded-full uppercase tracking-tighter">Koordinator Praktikum</span>
                    </div>
                </div>
            </div>
            
            {{-- Quick Info --}}
            <div class="hidden lg:block text-right">
                <p class="text-amber-100 text-[10px] font-bold uppercase tracking-widest">Status Sistem</p>
                <p class="text-white font-black text-lg">Monitoring Aktif</p>
            </div>
        </div>

        {{-- Dekorasi Abstrak (Beautifier) --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-amber-400/20 rounded-full -ml-10 -mb-10 blur-2xl"></div>
    </div>

    {{-- 2. Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $dStats = [
                ['label' => 'Total Mahasiswa', 'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->count(), 'icon' => '🎓', 'color' => 'bg-sky-50 border-sky-100',   'text' => 'text-sky-600'],
                ['label' => 'Mahasiswa Remidi', 'value' => \App\Models\Submission::where('score', '<', 75)->count(), 'icon' => '⚠️', 'color' => 'bg-red-50 border-red-100', 'text' => 'text-red-600'],
                ['label' => 'Validasi Soal',    'value' => \App\Models\Module::where('is_approved', false)->count(), 'icon' => '📋', 'color' => 'bg-emerald-50 border-emerald-100','text' => 'text-emerald-600'],
            ];
        @endphp
        @foreach ($dStats as $s)
            <div class="bg-white rounded-3xl border {{ $s['color'] }} p-6 flex items-center gap-5 shadow-sm hover:shadow-md transition-all group">
                <div class="w-14 h-14 rounded-2xl {{ $s['color'] }} border flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform">
                    {{ $s['icon'] }}
                </div>
                <div>
                    <p class="text-2xl font-black {{ $s['text'] }}">{{ $s['value'] }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- 3. Mahasiswa Bimbingan --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-gray-900 uppercase tracking-tight text-sm">Mahasiswa Bimbingan</h3>
                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
            </div>
            @php $mahasiswas = \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->latest()->take(5)->get(); @endphp
            <div class="space-y-4">
                @forelse ($mahasiswas as $m)
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-sm font-black border border-gray-100 group-hover:bg-amber-500 group-hover:text-white transition-all">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate group-hover:text-amber-600 transition-colors">{{ $m->name }}</p>
                            <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $m->username }}</p>
                        </div>
                        <span class="text-[10px] font-black text-gray-300 uppercase italic">Active</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Belum ada mahasiswa.</p>
                @endforelse
            </div>
        </div>

        {{-- 4. Logbook Terbaru --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
            <h3 class="font-black text-gray-900 uppercase tracking-tight text-sm mb-6">Logbook Terbaru</h3>
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-4 border border-amber-100">
                    <span class="text-4xl">📖</span>
                </div>
                <p class="text-sm font-bold text-gray-800">Belum ada bimbingan</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest">Semua laporan telah ditanggapi</p>
            </div>
        </div>

        {{-- 5. Tabel Monitoring Remidi --}}
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-black text-gray-900 uppercase tracking-tight text-red-600">Daftar Mahasiswa Remidi (QC)</h3>
                    <p class="text-[10px] text-gray-400 font-medium italic mt-1 uppercase tracking-widest">Nilai di bawah KKM (75)</p>
                </div>
                <a href="{{ route('dosen-pembimbing.reviews') }}" class="px-5 py-2 bg-amber-50 text-amber-600 rounded-xl text-[10px] font-black hover:bg-amber-600 hover:text-white transition-all uppercase tracking-widest shadow-sm">Lihat semua →</a>
            </div>
            
            @php 
                $remidis = \App\Models\Submission::with('user','module')->where('score', '<', 75)->latest()->get(); 
            @endphp
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="pb-4 px-4">Mahasiswa</th>
                            <th class="pb-4 px-4">Modul</th>
                            <th class="pb-4 px-4">Nilai</th>
                            <th class="pb-4 px-4 text-center">Status QC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($remidis as $r)
                        <tr class="group hover:bg-red-50/30 transition-colors">
                            <td class="py-5 px-4">
                                <p class="text-sm font-bold text-gray-800">{{ $r->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $r->user->username }}</p>
                            </td>
                            <td class="py-5 px-4 text-xs font-medium text-gray-500">{{ $r->module->title }}</td>
                            <td class="py-5 px-4 font-black text-red-600 text-lg">{{ $r->score }}</td>
                            <td class="py-5 px-4 text-center">
                                <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border border-red-200">
                                    Butuh Remidi
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center">
                                <div class="flex flex-col items-center opacity-30">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm font-black uppercase tracking-widest">Data Bersih</p>
                                    <p class="text-[10px] italic">Tidak ada mahasiswa di bawah KKM.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>