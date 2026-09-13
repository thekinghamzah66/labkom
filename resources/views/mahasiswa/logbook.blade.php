<x-layouts.app title="Logbook Bimbingan">
    <div class="p-6">
        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-fade-in">
                <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-sm">✓</div>
                <div class="text-emerald-800 font-bold text-sm">{{ session('success') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Input Logbook --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm sticky top-6">
                    <h3 class="text-xl font-black text-gray-900 uppercase mb-2">Lapor Progres</h3>
                    <p class="text-xs text-gray-400 mb-6 font-medium">Kirim laporan aktivitas praktikum ke Dosen Pembimbing.</p>
                    
                    <form action="{{ route('mahasiswa.logbook.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Aktivitas</label>
                            <input type="text" name="judul" placeholder="Misal: Menyelesaikan Modul 4" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-sky-500 outline-none" required>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Detail Progres</label>
                            <textarea name="aktivitas" rows="4" placeholder="Ceritakan kendala atau progres yang sudah dicapai..." class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-sky-500 outline-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-sky-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-sky-100 hover:bg-sky-700 transition-all">
                            Kirim Laporan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Riwayat Logbook & Feedback --}}
            <div class="lg:col-span-2">
                <h3 class="text-lg font-black text-gray-900 uppercase mb-6 tracking-tight">Riwayat Bimbingan</h3>
                
                <div class="space-y-6">
                    @forelse($logs as $log)
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-black text-gray-800">{{ $log->judul }}</h4>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $log->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if($log->feedback)
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg">DIBALAS</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-lg">MENUNGGU</span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $log->aktivitas }}</p>

                            {{-- Kotak Feedback dari Dosbim --}}
                            @if($log->feedback)
                                <div class="mt-6 p-5 bg-sky-50 rounded-2xl border border-sky-100 relative">
                                    <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-2">Feedback Dosen:</p>
                                    <p class="text-sm text-sky-800 italic">"{{ $log->feedback }}"</p>
                                    {{-- Dekorasi Quote --}}
                                    <div class="absolute top-4 right-6 opacity-10 text-sky-600">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.899 15.115 16 16.5 16C17.885 16 18.983 16.899 18.983 18L18.983 21L14.017 21ZM5.017 21L5.017 18C5.017 16.899 6.115 16 7.5 16C8.885 16 9.983 16.899 9.983 18L9.983 21L5.017 21ZM14.017 11L14.017 8C14.017 4.686 16.703 2 20 2L20 5C18.343 5 17 6.343 17 8L17 11L14.017 11ZM5.017 11L5.017 8C5.017 4.686 7.703 2 11 2L11 5C9.343 5 8 6.343 8 8L8 11L5.017 11Z"/></svg>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-20 text-center bg-white rounded-[2rem] border border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold italic">Belum ada laporan logbook yang kamu kirim.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>