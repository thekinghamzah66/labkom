<x-layouts.app title="Dashboard Mahasiswa">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Kiri: Info Sesi & Jadwal --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Informasi Praktikum Aktif</h3>
                
                @if($enrollment && $enrollment->session_name)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-violet-50 rounded-3xl border border-violet-100">
                            <p class="text-[10px] font-bold text-violet-400 uppercase tracking-widest">Jadwal Sesi</p>
                            <p class="text-2xl font-black text-violet-700 mt-1">{{ $enrollment->session_name }}</p>
                            <p class="text-sm font-bold text-violet-600 mt-2">{{ $enrollment->jam }} — Ruang {{ $enrollment->ruangan }}</p>
                        </div>
                        
                        <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Dosen Pembimbing</p>
                            <p class="text-lg font-bold text-emerald-700 mt-1">
                                {{ $enrollment->dosbim->name ?? 'Belum Ditentukan' }}
                            </p>
                            <p class="text-xs text-emerald-600 mt-1 italic">Tugas Akhir & Ujian Dosbim</p>
                        </div>
                    </div>
                @else
                    <div class="p-10 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                        <p class="text-gray-400 font-medium">Aslab belum memplot jadwal sesi untuk Anda.</p>
                    </div>
                @endif
            </div>

            {{-- Info Soal Terkini --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Tugas Praktikum</h3>
                    <a href="{{ route('mahasiswa.modules') }}" class="text-sm font-bold text-violet-600 hover:underline">Lihat Semua</a>
                </div>
                <p class="text-sm text-gray-500">Silakan cek menu "Modul & Tugas" untuk mendownload soal dari Aslab.</p>
            </div>
        </div>

        {{-- Kanan: Status Kelulusan --}}
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 text-center">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Status Kelulusan</h3>
                
                @if($enrollment)
                    @if($enrollment->status_aslab == 'lulus')
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-50">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-2xl font-black text-emerald-600 uppercase">LULUS</h4>
                        <p class="text-sm text-gray-500 mt-2">Selamat! Anda bisa melanjutkan ke tahap bimbingan Dosbim.</p>
                    @elseif($enrollment->status_aslab == 'remidi')
                        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h4 class="text-2xl font-black text-amber-600 uppercase">REMIDI</h4>
                        <p class="text-sm text-gray-500 mt-2">Nilai Anda belum mencukupi. Silakan hubungi Aslab untuk jadwal remidi.</p>
                    @elseif($enrollment->status_aslab == 'gagal')
                        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <h4 class="text-2xl font-black text-red-600 uppercase">GAGAL</h4>
                        <p class="text-sm text-gray-500 mt-2">Mohon maaf, Anda dinyatakan tidak lulus praktikum.</p>
                    @else
                        <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-400 uppercase">PROSES NILAI</h4>
                        <p class="text-sm text-gray-400 mt-2">Nilai Anda sedang diproses oleh Aslab.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>