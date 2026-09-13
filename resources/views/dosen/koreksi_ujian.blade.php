<x-layouts.app title="Penilaian Ujian">
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-2">Penilaian Ujian Bimbingan</h2>
            <p class="text-sm text-gray-400 mb-8">Berikan skor (porsi 60%) berdasarkan file jawaban yang dikirim mahasiswa.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="py-4 px-8">Mahasiswa</th>
                            <th class="py-4 px-8">File Jawaban</th>
                            <th class="py-4 px-8">Skor Saat Ini</th>
                            <th class="py-4 px-8 text-center">Input Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr class="bg-gray-50/50 hover:bg-white hover:shadow-md transition-all rounded-2xl">
                            <td class="py-4 px-8 rounded-l-2xl">
                                <p class="font-bold text-gray-800">{{ $s->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $s->user->username }}</p>
                            </td>
                            <td class="py-4 px-8">
                                <a href="{{ asset('storage/' . $s->jawaban_ujian_dosbim) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Lihat Jawaban
                                </a>
                            </td>
                            <td class="py-4 px-8">
                                <span class="text-xl font-black {{ $s->nilai_ujian_dosbim >= 65 ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $s->nilai_ujian_dosbim ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-8 rounded-r-2xl text-center">
                               {{-- Cari bagian form input nilai di tabel Dosbim dan ubah jadi ini --}}
<form action="{{ route('dosen-pembimbing.penilaian-ujian.simpan', $s->id) }}" method="POST" class="space-y-2">
    @csrf
    <div class="flex gap-2">
        <input type="number" name="nilai_ujian" class="w-16 p-2 border rounded-xl text-xs" placeholder="Skor" required>
        <select name="status" class="text-[10px] border-gray-200 rounded-xl">
            <option value="Lulus">Lulus</option>
            <option value="Remidi">Remidi</option>
            <option value="Gagal">Gagal</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
        </button>
    </div>
</form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center text-gray-400 italic">Belum ada jawaban mahasiswa yang masuk untuk praktikum ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>