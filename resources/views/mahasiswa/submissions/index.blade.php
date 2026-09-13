<x-layouts.app title="Tugas Praktikum">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm">
        <div class="mb-8">
            <h3 class="font-black text-gray-900 text-xl uppercase tracking-tight text-sky-600">Daftar Modul & Soal</h3>
            <p class="text-xs text-gray-400 mt-1">Pilih modul di bawah ini untuk melihat instruksi dan mengumpulkan jawaban.</p>
        </div>
        
        <div class="space-y-4">
            @forelse($modules as $m)
                {{-- Card per item tugas --}}
                <div class="flex flex-col md:flex-row items-center justify-between p-6 rounded-[2rem] border border-gray-50 hover:border-sky-100 hover:bg-sky-50/30 transition-all group">
                    <div class="flex items-center gap-6">
                        {{-- Icon Modul --}}
                        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
 <div>
    <p class="font-black text-lg text-gray-800 leading-tight group-hover:text-sky-600 transition-colors">{{ $m->title }}</p>
    <div class="flex items-center gap-3 mt-1.5">
        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest flex items-center gap-1">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    
    @if($m->deadline)
        {{-- Menggunakan format Indonesia --}}
        DEADLINE: {{ $m->deadline->translatedFormat('d F Y, H:i') }} WIB
    @else
        <span class="text-amber-500">TIDAK ADA BATAS WAKTU</span>
    @endif
</p>
    </div>
</div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-4 md:mt-0 w-full md:w-auto">
                        <a href="{{ route('mahasiswa.submissions.show', $m->id) }}" class="block text-center px-8 py-3 bg-sky-600 text-white text-xs font-black rounded-2xl shadow-lg shadow-sky-100 hover:bg-sky-700 transition-all uppercase tracking-widest active:scale-95">
                            Buka Tugas
                        </a>
                    </div>
                </div>
            @empty
                {{-- Tampilan jika database kosong --}}
                <div class="py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <p class="text-gray-400 font-bold italic">Belum ada tugas praktikum yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>