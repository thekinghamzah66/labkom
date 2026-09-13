<x-layouts.app title="Data Mahasiswa Praktikum">
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Antrean Plotting Sesi</h2>
                    <p class="text-sm text-gray-500">Daftar mahasiswa yang belum mendapatkan jadwal sesi.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-gray-400 text-sm uppercase tracking-widest">
                            <th class="px-6 py-3 font-semibold">Mahasiswa</th>
                            <th class="px-6 py-3 font-semibold">Username</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $item)
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors rounded-2xl">
                            <td class="px-6 py-4 rounded-l-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-700">{{ $item->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-600">{{ $item->user->username }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-lg text-xs font-bold uppercase">Menunggu Sesi</span>
                            </td>
                            <td class="px-6 py-4 rounded-r-2xl text-center">
                                {{-- Tombol untuk buka Modal --}}
                                <button onclick="openModal('{{ $item->id }}', '{{ $item->user->name }}')" 
                                    class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all">
                                    Plotting Sesi
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-400 font-medium">Tidak ada mahasiswa dalam antrean.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL INPUT SESI --}}
    <div id="modalSesi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 animate-fade-in">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Tentukan Sesi</h3>
            <p class="text-gray-500 mb-6">Mahasiswa: <span id="mhsName" class="font-bold text-violet-600"></span></p>

            <form id="formSesi" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Sesi</label>
                    <input type="text" name="session_name" placeholder="Contoh: Sesi 1" required
                        class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jam Mulai</label>
                        <input type="text" name="jam" placeholder="18:00" required
                            class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ruangan</label>
                        <input type="text" name="ruangan" placeholder="A101" required
                            class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition-all">
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 font-bold text-gray-500 hover:bg-gray-100 rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 font-bold bg-violet-600 text-white rounded-2xl hover:bg-violet-700 shadow-lg shadow-violet-200 transition-all">Simpan Sesi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, name) {
            document.getElementById('mhsName').innerText = name;
            document.getElementById('formSesi').action = `/aslab/mahasiswa/${id}/assign`;
             document.getElementById('formSesi').action = "/aslab/mahasiswa/" + id + "/assign";
            document.getElementById('modalSesi').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('modalSesi').classList.add('hidden');
        }
    </script>
</x-layouts.app>