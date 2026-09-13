<x-layouts.app title="Kelulusan Final">
    <div class="space-y-6">
        {{-- Info Praktikum Aktif --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded-lg border border-indigo-100 tracking-widest">Otoritas Kalab</span>
                <h1 class="text-3xl font-black text-gray-900 mt-3 tracking-tighter uppercase font-display">Penentuan Kelulusan Final</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Praktikum Aktif: <span class="font-bold text-indigo-600">{{ \App\Models\Practicum::find(session('active_kalab_practicum_id'))->name ?? 'Tidak ada praktikum yang dipilih' }}</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kalab.select-practicum') }}" class="px-6 py-3 bg-gray-50 text-gray-500 rounded-2xl text-xs font-black uppercase hover:bg-gray-100 transition-all">Ganti Praktikum</a>
                <a href="{{ route('kalab.export') }}" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Rekap
                </a>
            </div>
        </div>

        {{-- Tabel Rekapitulasi --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Daftar Mahasiswa & Perhitungan Skor Akhir</h3>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-black">Formula: (Nilai Aslab 40% + Nilai Dosbim 60%) — Kelulusan ≥ 65</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-8">Mahasiswa</th>
                            <th class="py-5 px-8">Dosen Pembimbing</th>
                            <th class="py-5 px-8 text-center">Skor Aslab (40%)</th>
                            <th class="py-5 px-8 text-center">Skor Dosbim (60%)</th>
                            <th class="py-5 px-8 text-center">Total Nilai</th>
                            <th class="py-5 px-8 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($students as $s)
                        @php
                            // Mengambil nilai dari kolom yang sudah kita buat sebelumnya
                            $nilaiAslab = $s->nilai_awal ?? 0;
                            $nilaiDosbim = $s->nilai_ujian_dosbim ?? 0;
                            $nilaiAkhir = ($nilaiAslab * 0.4) + ($nilaiDosbim * 0.6);
                            $isLulus = ($nilaiAkhir >= 65);
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-6 px-8">
                                {{-- Keamanan: Gunakan ?? untuk mencegah error jika user dihapus --}}
                                <p class="font-bold text-gray-800 leading-none group-hover:text-indigo-600 transition-colors">{{ $s->user->name ?? 'User Tidak Ditemukan' }}</p>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">{{ $s->user->username ?? '-' }}</p>
                            </td>
                            <td class="py-6 px-8">
                                {{-- Keamanan: Gunakan ?? untuk mencegah error jika dosbim belum di-set --}}
                                <p class="text-xs font-bold text-indigo-400">
                                    {{ $s->dosbim->name ?? 'Belum Ada Pembimbing' }}
                                </p>
                            </td>
                            <td class="py-6 px-8 text-center">
                                <span class="text-sm font-bold text-gray-500">{{ $nilaiAslab }}</span>
                            </td>
                            <td class="py-6 px-8 text-center">
                                <span class="text-sm font-bold text-gray-500">{{ $nilaiDosbim }}</span>
                            </td>
                            <td class="py-6 px-8 text-center">
                                <span class="text-xl font-black {{ $isLulus ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ number_format($nilaiAkhir, 1) }}
                                </span>
                            </td>
                            <td class="py-6 px-8 text-center">
                                @if($isLulus)
                                    <span class="px-4 py-1.5 bg-emerald-100 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-200 shadow-sm shadow-emerald-50">
                                        LULUS
                                    </span>
                                @else
                                    <span class="px-4 py-1.5 bg-red-100 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-200 shadow-sm shadow-red-50">
                                        GAGAL
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-24 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <p class="text-gray-400 font-bold italic font-display">Belum ada data mahasiswa yang memenuhi syarat kelulusan akhir.</p>
                                <p class="text-[10px] text-gray-300 mt-1 uppercase">Nilai akan muncul setelah Dosbim memberikan nilai ujian.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>