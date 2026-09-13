<x-layouts.app title="Penilaian Ujian Bimbingan">
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-6">Koreksi Jawaban Mahasiswa</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="py-4 px-6">Mahasiswa</th>
                            <th class="py-4 px-6">File Jawaban</th>
                            <th class="py-4 px-6">Skor Ujian (60%)</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr class="bg-gray-50/50 hover:bg-white hover:shadow-md transition-all">
                            <td class="py-4 px-6 rounded-l-2xl">
                                <p class="font-bold text-gray-800">{{ $s->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $s->user->username }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ asset('storage/' . $s->jawaban_ujian_dosbim) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Lihat Jawaban
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-lg font-black {{ $s->nilai_ujian_dosbim >= 65 ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $s->nilai_ujian_dosbim ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 rounded-r-2xl text-center">
                              {{-- Ubah baris 36 menjadi ini: --}}
<form action="{{ route('dosen-pembimbing.penilaian.simpan', $s->id) }}" method="POST" class="flex items-center gap-2 justify-center">
                                    @csrf
                                    <input type="number" name="nilai_ujian" class="w-20 p-2 bg-white border border-gray-200 rounded-xl text-xs font-bold" placeholder="0-100" required>
                                    <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 italic">Belum ada mahasiswa bimbingan yang mengirimkan jawaban ujian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>