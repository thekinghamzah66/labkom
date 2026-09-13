<x-layouts.app title="Materi Praktikum">
    <div class="mb-8">
        <h1 class="text-2xl font-display font-bold text-gray-900">Repositori Materi & Soal</h1>
        <p class="text-sm text-gray-500 mt-1">Unduh modul praktikum dan pelajari materi video di sini.</p>
    </div>

    {{-- Grid Daftar Modul --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($modules as $m)
        <div class="bg-white rounded-3xl border border-gray-100 p-6 hover:shadow-lg transition-all group">
            <div class="flex justify-between items-start mb-4">
                {{-- Icon dinamis berdasarkan tipe --}}
                <div class="w-12 h-12 {{ $m->type == 'PDF' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }} rounded-2xl flex items-center justify-center shadow-sm">
                    @if($m->type == 'PDF')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                <span class="text-[10px] font-black px-2 py-1 bg-gray-50 text-gray-400 rounded-lg group-hover:bg-sky-50 group-hover:text-sky-600 transition-colors uppercase tracking-widest">{{ $m->type }}</span>
            </div>

            <h3 class="font-bold text-gray-900 group-hover:text-sky-600 transition-colors">{{ $m->title }}</h3>
            <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $m->description ?? 'Tidak ada deskripsi.' }}</p>

            <div class="mt-6 flex gap-3">
                {{-- Download File --}}
                <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="flex-1 text-center py-2 bg-gray-50 text-gray-600 text-xs font-bold rounded-xl hover:bg-sky-500 hover:text-white transition-all">
                    Unduh Materi
                </a>
                {{-- Link ke Soal --}}
                <a href="{{ route('mahasiswa.submissions.show', $m->id) }}" class="flex-1 text-center py-2 border border-sky-100 text-sky-600 text-xs font-bold rounded-xl hover:bg-sky-50 transition-all">
                    Lihat Soal
                </a>
            </div>
        </div>
        @empty
            <div class="col-span-3 py-20 text-center text-gray-400 italic">
                Belum ada materi yang diunggah oleh Asisten Laboratorium.
            </div>
        @endforelse
    </div>
</x-layouts.app>