<x-layouts.app title="Bimbingan Logbook">
    <div class="p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight text-amber-600">Bimbingan Logbook</h1>
            <p class="text-sm text-gray-400 mt-1">Pantau progres harian dan berikan asistensi pada mahasiswa.</p>
        </div>

        <div class="space-y-6">
            @forelse($logbooks as $log)
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center font-black">
                                {{ substr($log->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-gray-900">{{ $log->user->name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $log->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($log->feedback)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg">TERJAWAB</span>
                        @else
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-lg">BUTUH RESPON</span>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                        <p class="text-xs font-black text-gray-400 uppercase mb-2">Laporan Progres:</p>
                        <p class="font-bold text-gray-800 mb-2">{{ $log->judul }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $log->aktivitas }}</p>
                    </div>

                    <form action="{{ route('dosen-pembimbing.logbook.comment', $log->id) }}" method="POST" class="space-y-4">
                        @csrf @method('PATCH')
                        <textarea name="feedback" rows="2" placeholder="Berikan arahan atau feedback bimbingan..." class="w-full p-4 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 outline-none transition-all">{{ $log->feedback }}</textarea>
                        <button type="submit" class="bg-amber-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-100 hover:bg-amber-600 transition-all">
                            Kirim Feedback Bimbingan
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-[2rem] border border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold italic">Belum ada mahasiswa yang mengirimkan logbook.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>