<x-layouts.app title="Penilaian Jawaban Mahasiswa">
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Jawaban Tugas Masuk</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-gray-400 text-xs uppercase tracking-widest">
                            <th class="px-6 py-3">Mahasiswa</th>
                            <th class="px-6 py-3">Tugas / Modul</th>
                            <th class="px-6 py-3">File Jawaban</th>
                            <th class="px-6 py-3">Skor & Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-all rounded-2xl">
                            <td class="px-6 py-4 rounded-l-2xl">
                                <p class="font-bold text-gray-800">{{ $sub->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold">{{ $sub->user->username }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-violet-600">{{ $sub->module->title }}</p>
                                <p class="text-[10px] text-gray-400 italic">Dikirim: {{ $sub->created_at->format('d M Y, H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-violet-50 hover:text-violet-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Lihat Jawaban
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($sub->score)
                                    <div class="flex flex-col">
                                        <span class="text-lg font-black text-gray-800 leading-none">{{ $sub->score }}</span>
                                        <span class="text-[9px] font-bold uppercase mt-1 {{ $sub->status == 'Lulus' ? 'text-emerald-500' : ($sub->status == 'Remidi' ? 'text-amber-500' : 'text-red-500') }}">
                                            {{ $sub->status }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-widest">Belum Dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 rounded-r-2xl text-center">
                                {{-- Tombol Modal Penilaian --}}
                                <button onclick="openGradingModal('{{ $sub->id }}', '{{ $sub->user->name }}', '{{ $sub->module->title }}')" 
                                    class="bg-violet-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-violet-700 transition-all">
                                    Beri Nilai
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-20 text-gray-400 italic">Belum ada jawaban mahasiswa yang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PENILAIAN --}}
    <div id="gradingModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 animate-fade-in shadow-2xl">
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Penilaian Tugas</h3>
            <p id="modalMhs" class="text-sm text-gray-500 mb-6"></p>

            <form id="formGrade" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Skor Akhir (0-100)</label>
                    <input type="number" name="score" id="inputScore" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tentukan Status</label>
                    <select name="status" id="inputStatus" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-violet-500 font-bold">
                        <option value="Lulus">LULUS</option>
                        <option value="Remidi">REMIDI</option>
                        <option value="Gagal">GAGAL (TIDAK LULUS)</option>
                    </select>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeGradingModal()" class="flex-1 py-4 font-bold text-gray-400 hover:bg-gray-50 rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 font-bold bg-violet-600 text-white rounded-2xl hover:bg-violet-700 shadow-lg shadow-violet-200 transition-all">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openGradingModal(id, name, task) {
            document.getElementById('modalMhs').innerHTML = `Mahasiswa: <b>${name}</b><br>Tugas: ${task}`;
            document.getElementById('formGrade').action = `/aslab/grading/${id}`;
            document.getElementById('gradingModal').classList.remove('hidden');
        }
        function closeGradingModal() {
            document.getElementById('gradingModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>