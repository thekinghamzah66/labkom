<x-layouts.app title="Bimbingan Mahasiswa">
    <div class="space-y-6">
        {{-- Header & Info --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight font-display">Mahasiswa Bimbingan</h2>
                <p class="text-sm text-gray-400 mt-1">Kelola tugas akhir untuk {{ $students->count() }} mahasiswa bimbingan Anda.</p>
            </div>
            <div class="px-5 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-black border border-indigo-100 uppercase">
                {{ \App\Models\Practicum::find(session('active_dosbim_practicum_id'))->name }}
            </div>
        </div>

        {{-- FITUR BARU: BROADCAST SOAL (UPLOAD SEKALIGUS) --}}
        <div class="bg-gradient-to-r from-indigo-600 to-violet-700 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 text-white">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-xl font-bold uppercase tracking-tight">Broadcast Soal Ujian</h3>
                    <p class="text-indigo-100 text-sm mt-1">Upload satu file untuk dikirimkan ke <b>seluruh</b> mahasiswa bimbingan di praktikum ini.</p>
                </div>
                <form action="{{ route('dosen-pembimbing.mentoring.bulk-upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    @csrf
                    <input type="file" name="soal_ujian" required class="text-xs bg-white/10 p-2 rounded-xl border border-white/20 focus:outline-none file:hidden cursor-pointer">
                    <button type="submit" class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-black text-xs uppercase hover:bg-indigo-50 transition-all shadow-lg">
                        Kirim ke Semua
                    </button>
                </form>
            </div>
        </div>

        {{-- Grid Daftar Mahasiswa (Tetap ada sebagai Monitoring) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($students as $s)
            <div class="p-6 border border-gray-100 rounded-[2rem] bg-white group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center font-black">
                        {{ strtoupper(substr($s->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-black text-gray-800 text-sm">{{ $s->user->name }}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $s->user->username }}</p>
                    </div>
                </div>

                {{-- Status Per Mahasiswa --}}
                <div class="pt-4 border-t border-dashed border-gray-100">
                    @if($s->soal_ujian_dosbim)
                        <div class="flex items-center gap-2 text-emerald-500 font-bold text-[10px] uppercase">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                            Soal Terkirim
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-amber-500 font-bold text-[10px] uppercase">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Belum Ada Soal
                        </div>
                    @endif
                </div>
            </div>
            @empty
                {{-- Bagian Empty State tetap sama --}}
            @endforelse
        </div>
    </div>
</x-layouts.app>