<x-layouts.app title="E-KHS Praktikum">
    <div class="p-6">
        {{-- Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight text-sky-600 font-display">Kartu Hasil Studi (E-KHS)</h1>
                <p class="text-sm text-gray-400 mt-1 font-medium">Pantau nilai dan feedback dari Asisten Laboratorium secara real-time.</p>
            </div>
            {{-- Info Praktikum Aktif --}}
            <div class="px-6 py-3 bg-sky-50 border border-sky-100 rounded-2xl shadow-sm">
                <p class="text-[10px] font-black text-sky-400 uppercase tracking-widest leading-none">Praktikum Saat Ini</p>
                <p class="text-sm font-bold text-sky-700 mt-1">
                    {{ \App\Models\Practicum::find(session('active_mhs_practicum_id'))->name ?? 'Tidak Diketahui' }}
                </p>
            </div>
        </div>

        {{-- Tabel Nilai Praktikum (Tahap Aslab) --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Detail Nilai Tugas & Modul</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="py-5 px-8 border-b border-gray-100">Materi / Modul</th>
                            <th class="py-5 px-8 border-b border-gray-100">Skor</th>
                            <th class="py-5 px-8 border-b border-gray-100">Status Kelulusan</th>
                            <th class="py-5 px-8 border-b border-gray-100">Catatan Aslab</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($progress as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full {{ $item->type == 'Tugas' ? 'bg-rose-400' : 'bg-blue-400' }}"></div>
                                    <span class="font-bold text-gray-800">{{ $item->title }}</span>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                @if($item->userSubmission && $item->userSubmission->score !== null)
                                    <span class="text-xl font-black {{ $item->userSubmission->score >= 65 ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $item->userSubmission->score }}
                                    </span>
                                @else
                                    <span class="text-gray-300 font-bold">-</span>
                                @endif
                            </td>
                            <td class="py-6 px-8">
                                @if($item->userSubmission)
                                    @php $st = $item->userSubmission->status; @endphp
                                    @if($st == 'Lulus')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase border border-emerald-200">Lulus</span>
                                    @elseif($st == 'Remidi')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-lg text-[10px] font-black uppercase border border-amber-200">Remidi</span>
                                    @elseif($st == 'Gagal')
                                        <span class="px-3 py-1 bg-rose-100 text-rose-600 rounded-lg text-[10px] font-black uppercase border border-rose-200">Gagal</span>
                                    @else
                                        <span class="px-3 py-1 bg-sky-50 text-sky-500 rounded-lg text-[10px] font-black uppercase border border-sky-100">Sedang Direview</span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-[10px] font-black uppercase border border-gray-200">Belum Kumpul</span>
                                @endif
                            </td>
                            <td class="py-6 px-8">
                                <p class="text-xs text-gray-500 italic max-w-xs leading-relaxed">
                                    {{ $item->userSubmission->feedback ?? 'Belum ada catatan dari aslab.' }}
                                </p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center text-gray-400 font-medium italic">Data nilai belum tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- --- FITUR BARU: RINGKASAN KELULUSAN AKHIR (GABUNGAN 40/60) --- --}}
        @php
            $enroll = auth()->user()->enrollments->where('practicum_id', session('active_mhs_practicum_id'))->first();
            $nilaiAslab = $enroll->nilai_awal ?? 0;
            $nilaiDosbim = $enroll->nilai_ujian_dosbim ?? 0;
            $statusDosbim = $enroll->status_dosbim ?? 'pending';

            // Hitung Skor Akhir: (40% Aslab + 60% Dosbim)
            $skorAkhir = ($nilaiAslab * 0.4) + ($nilaiDosbim * 0.6);
            $isLulusFinal = ($skorAkhir >= 65 && $statusDosbim == 'lulus');
        @endphp

        @if($enroll)
        <div class="p-8 bg-gradient-to-br from-gray-900 to-indigo-950 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-200 animate-fade-in border-4 border-white/5">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-8">
                {{-- Label Kiri --}}
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 rounded-lg border border-indigo-500/30 mb-4">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">Hasil Akumulasi Final</span>
                    </div>
                    <h3 class="text-3xl font-black uppercase tracking-tighter font-display leading-none">Rekapitulasi Kelulusan</h3>
                    <p class="text-indigo-200/50 text-sm mt-2 font-medium">Bobot: 40% Praktikum + 60% Ujian Dosen Pembimbing</p>
                </div>

                {{-- Angka & Status Kanan --}}
                <div class="flex flex-col md:flex-row items-center gap-10">
                    {{-- Detail Komponen --}}
                    <div class="grid grid-cols-2 gap-4 text-center md:text-left">
                        <div>
                            <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest">Skor Aslab</p>
                            <p class="text-xl font-bold">{{ $nilaiAslab }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest">Skor Dosbim</p>
                            <p class="text-xl font-bold">{{ $nilaiDosbim }}</p>
                        </div>
                    </div>

                    <div class="h-12 w-px bg-white/10 hidden md:block"></div>

                    {{-- Skor Akhir --}}
                    <div class="text-center">
                        <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mb-1">Total Skor</p>
                        <p class="text-6xl font-black {{ $isLulusFinal ? 'text-emerald-400' : 'text-rose-400' }} tracking-tighter font-display">
                            {{ number_format($skorAkhir, 1) }}
                        </p>
                    </div>

                    {{-- Badge Status --}}
                    <div class="text-center">
                        <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mb-3">Status Final</p>
                        @if($statusDosbim == 'pending')
                             <span class="px-6 py-3 bg-white/10 text-white rounded-2xl text-xs font-black uppercase tracking-widest border border-white/10">Menunggu Ujian</span>
                        @elseif($isLulusFinal)
                            <div class="px-6 py-3 bg-emerald-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 border-b-4 border-emerald-700">
                                LULUS FINAL
                            </div>
                        @else
                            <div class="px-6 py-3 bg-rose-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-rose-500/20 border-b-4 border-rose-700">
                                TIDAK LULUS
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>