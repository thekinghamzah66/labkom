<x-layouts.app title="Dashboard ASLAB">

    {{-- 1. Greeting Card - Emerald Theme --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 rounded-3xl p-8 mb-8 shadow-lg shadow-emerald-100">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 p-3 shadow-inner">
                   <img src="{{ asset('img/logo-untag.jpg') }}" alt="Logo Untag" class="w-full h-full object-contain">
                </div>
                <div>
                    <p class="text-emerald-100 text-xs font-black uppercase tracking-[0.2em] mb-1">Asisten Laboratorium</p>
                    <h2 class="font-display text-3xl font-black text-white leading-tight">{{ auth()->user()->name }}</h2>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold rounded-full border border-white/20">ID: {{ auth()->user()->username }}</span>
                        <span class="px-3 py-1 bg-emerald-400 text-emerald-900 text-[10px] font-bold rounded-full">Sistem Aktif</span>
                    </div>
                </div>
            </div>
            
            {{-- Quick Stats Ringkas --}}
            <div class="flex gap-4">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <p class="text-[10px] text-emerald-100 uppercase font-bold">Modul</p>
                    <p class="text-2xl font-black text-white">08</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <p class="text-[10px] text-emerald-100 uppercase font-bold">Hadir</p>
                    <p class="text-2xl font-black text-white">85%</p>
                </div>
            </div>
        </div>
        {{-- Dekorasi Abstrak --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    </div>

    {{-- 2. Stats Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $aStats = [
                ['label' => 'Total Mahasiswa', 'value' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->count(), 'icon' => '🎓', 'color' => 'bg-sky-50 border-sky-100', 'text' => 'text-sky-600'],
                ['label' => 'Materi Praktikum', 'value' => '12', 'icon' => '📚', 'color' => 'bg-emerald-50 border-emerald-100', 'text' => 'text-emerald-600'],
                ['label' => 'Tugas Perlu Review', 'value' => '05', 'icon' => '⏳', 'color' => 'bg-amber-50 border-amber-100', 'text' => 'text-amber-600'],
            ];
        @endphp
        @foreach ($aStats as $s)
            <div class="bg-white rounded-3xl border border-gray-100 p-6 flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl {{ $s['color'] }} flex items-center justify-center text-3xl shadow-inner">{{ $s['icon'] }}</div>
                <div>
                    <p class="text-2xl font-black {{ $s['text'] }}">{{ $s['value'] }}</p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 3. Daftar Mahasiswa Aktif --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-gray-900 text-lg uppercase tracking-tight">Mahasiswa Aktif</h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            @php $mahasiswas = \App\Models\User::whereHas('role', fn($q) => $q->where('slug','mahasiswa'))->where('is_active',true)->latest()->take(5)->get(); @endphp
            <div class="space-y-4">
                @forelse ($mahasiswas as $m)
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-black border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $m->name }}</p>
                            <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $m->username }}</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-black text-emerald-600">ONLINE</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6 italic">Belum ada mahasiswa.</p>
                @endforelse
            </div>
            <button class="w-full mt-6 py-3 border border-gray-100 rounded-2xl text-xs font-bold text-gray-400 hover:bg-gray-50 transition-colors uppercase tracking-widest">Lihat Semua</button>
        </div>

        {{-- 4. Presensi Digital QR (Widget Pro) --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm flex flex-col items-center justify-center text-center group">
            <div class="mb-6">
                <h3 class="font-black text-gray-900 text-lg uppercase tracking-tight">Presensi QR</h3>
                <p class="text-xs text-gray-400 mt-1">Generate QR untuk praktikum hari ini</p>
            </div>
            
            <div class="w-48 h-48 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center p-6 mb-6 group-hover:border-emerald-300 transition-colors">
                <svg class="w-16 h-16 text-gray-200 group-hover:text-emerald-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <p class="text-[10px] font-bold text-gray-300 mt-4 uppercase tracking-widest">QR Ready to Generate</p>
            </div>
            
            <a href="{{ route('aslab.attendance') }}" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Generate QR Code</a>
        </div>

        {{-- 5. CMS Materi (Quick Action) --}}
        <div class="bg-gray-900 rounded-3xl p-8 shadow-sm flex flex-col justify-between overflow-hidden relative">
            <div class="relative z-10">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20">CMS MATERI</span>
                <h3 class="text-xl font-black text-white mt-4 leading-tight">Kelola Materi &<br>Modul Soal</h3>
                <p class="text-gray-400 text-xs mt-2">Upload modul PDF atau link video tutorial mahasiswa.</p>
            </div>
            <a href="{{ route('aslab.modules.index') }}" class="relative z-10 w-full bg-white text-gray-900 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-center mt-8 hover:bg-emerald-400 hover:text-emerald-950 transition-all">Buka CMS Materi</a>
            
            {{-- Background Icon --}}
            <svg class="absolute -right-10 -bottom-10 w-40 h-40 text-white/5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.993 7.993 0 002 12a7.998 7.998 0 007 7.917V4.804zm2 0V19.917A7.998 7.998 0 0018 12a7.993 7.993 0 00-7-7.196z"></path></svg>
        </div>

        {{-- 6. Tabel Penilaian Terbaru (Feedback System) --}}
        <div class="lg:col-span-3 bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-black text-gray-900 text-lg uppercase tracking-tight text-emerald-600">Sistem Penilaian & Feedback</h3>
                    <p class="text-xs text-gray-400 mt-1">Berikan nilai dan koreksi pada tugas mahasiswa</p>
                </div>
                <a href="{{ route('aslab.grading') }}" class="text-xs font-black text-emerald-500 hover:underline">Kelola Semua Penilaian &rarr;</a>
            </div>
            
            {{-- Tampilan jika data kosong tetap dibuat rapi --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="pb-4 px-4 text-emerald-600">Mahasiswa</th>
                            <th class="pb-4 px-4 text-emerald-600">Modul</th>
                            <th class="pb-4 px-4 text-emerald-600">File</th>
                            <th class="pb-4 px-4 text-emerald-600">Status</th>
                            <th class="pb-4 px-4 text-center text-emerald-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {{-- Dummy Data untuk simulasi tampilan --}}
                        <tr class="group">
                            <td class="py-4 px-4">
                                <p class="text-sm font-bold text-gray-800">Fransiskus Agustinus</p>
                                <p class="text-[10px] text-gray-400">NIM: 1462200121</p>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">Modul 4: Database</td>
                            <td class="py-4 px-4">
                                <span class="text-[10px] font-bold text-sky-500 bg-sky-50 px-2 py-1 rounded-lg">jawaban.pdf</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase">Perlu Feedback</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="#" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black hover:bg-emerald-600 hover:text-white transition-all uppercase tracking-widest">Beri Nilai</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>