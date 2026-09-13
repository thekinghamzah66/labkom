<x-layouts.app title="Validation Gate">
    <div class="p-6">
        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-fade-in">
                <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-sm">✓</div>
                <div class="text-emerald-800 font-bold text-sm">{{ session('success') }}</div>
            </div>
        @endif

        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight text-amber-600">Validation Gate</h1>
            <p class="text-sm text-gray-400 mt-1">Setujui atau tolak materi/soal yang dibuat oleh Asisten Laboratorium.</p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($pendingModules as $m)
                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-6">
                        {{-- Icon Berdasarkan Tipe --}}
                        <div class="w-16 h-16 {{ $m->type == 'PDF' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }} rounded-2xl flex items-center justify-center shadow-inner">
                            @if($m->type == 'PDF')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-black px-2 py-0.5 bg-gray-100 text-gray-400 rounded uppercase tracking-widest">{{ $m->type }}</span>
                            <h3 class="text-lg font-black text-gray-800 mt-1">{{ $m->title }}</h3>
                            <p class="text-xs text-gray-400 italic">Diunggah pada: {{ $m->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Tombol Lihat File --}}
                        <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="flex-1 md:flex-none px-6 py-3 bg-gray-50 text-gray-500 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all text-center">Preview</a>
                        
                        {{-- Form Reject --}}
                        <form action="{{ route('dosen-pembimbing.validation.reject', $m->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Tolak dan hapus soal ini?')" class="w-full px-6 py-3 bg-red-50 text-red-500 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">Reject</button>
                        </form>

                        {{-- Form Approve --}}
                        <form action="{{ route('dosen-pembimbing.validation.approve', $m->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full px-8 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all italic italic">Approve ✓</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-[2rem] border border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold italic">Tidak ada materi atau soal yang menunggu validasi.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>