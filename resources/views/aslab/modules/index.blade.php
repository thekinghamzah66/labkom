<x-layouts.app title="CMS MATERI & TUGAS">
    <div class="p-6" x-data="{ openAdd: false, openEdit: false, currModule: {} }">
        
        {{-- 1. NOTIFIKASI --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-xs font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 2. HEADER --}}
        <div class="relative overflow-hidden mb-8 p-8 bg-emerald-900 rounded-[2rem] text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="relative z-10 text-center md:text-left">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest">Management Mode</span>
                <h1 class="text-3xl font-black uppercase mt-3 tracking-tighter">Manajemen Modul & Tugas</h1>
                <p class="text-emerald-300/60 text-xs mt-1 font-medium italic">Kelola materi pembelajaran dan soal praktikum mahasiswa.</p>
            </div>
            <button @click="openAdd = true" class="relative z-10 bg-emerald-500 hover:bg-emerald-400 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg flex items-center gap-2 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Konten
            </button>
        </div>

        {{-- 3. TABEL DATA --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-8">Materi / Tugas</th>
                            <th class="py-5 px-8">Kategori</th>
                            <th class="py-5 px-8">Deadline</th>
                            <th class="py-5 px-8 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($modules as $m)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-5 px-8">
                                <p class="font-bold text-gray-900 text-sm">{{ $m->title }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ Str::limit($m->description, 40) }}</p>
                            </td>
                            <td class="py-5 px-8">
                                <span class="px-3 py-1 {{ $m->type == 'Materi' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} rounded-lg text-[10px] font-black uppercase border">
                                    {{ $m->type }}
                                </span>
                            </td>
                            <td class="py-5 px-8">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">{{ $m->deadline ? \Carbon\Carbon::parse($m->deadline)->format('d M, H:i') : '-' }}</p>
                            </td>
                            <td class="py-5 px-8">
                                <div class="flex items-center justify-center gap-4">
                                    <button @click="openEdit = true; currModule = {{ json_encode($m) }}" class="text-[10px] font-black text-emerald-600 uppercase hover:text-emerald-800">Edit</button>
                                    <form action="{{ route('aslab.modules.destroy', $m->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black text-red-400 uppercase hover:text-red-600" onclick="return confirm('Hapus materi?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-gray-400 italic text-sm">Belum ada konten praktikum.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. MODAL TAMBAH (Tetap biarkan seperti sebelumnya) --}}
        <div x-show="openAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-8 max-w-lg w-full shadow-2xl">
                <h3 class="text-xl font-black text-gray-900 uppercase mb-6">Upload Konten Baru</h3>
                <form action="{{ route('aslab.modules.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Judul</label>
                        <input type="text" name="title" required class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Kategori</label>
                        <select name="type" required class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm font-bold">
                            <option value="Materi">Materi (Video/PDF)</option>
                            <option value="Tugas">Tugas (Soal Praktikum)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">Deadline</label>
                            <input type="datetime-local" name="deadline" required class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-xs">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">File Soal</label>
                            <input type="file" name="file_materi" required class="mt-2 text-[10px]">
                        </div>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                        <label class="text-[10px] font-black text-amber-600 uppercase">Soal Remidi (Opsional)</label>
                        <input type="file" name="file_remidi" class="text-[10px] mt-2">
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="openAdd = false" class="flex-1 py-4 text-xs font-black text-gray-400 uppercase">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 5. MODAL EDIT (Tetap biarkan) --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-8 max-w-lg w-full shadow-2xl">
                <h3 class="text-xl font-black text-gray-900 uppercase mb-6">Edit Konten</h3>
                <form :action="'/aslab/modules/' + currModule.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Judul</label>
                        <input type="text" name="title" x-model="currModule.title" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm">
                    </div>
                    <div class="flex gap-4">
                        <button type="button" @click="openEdit = false" class="flex-1 py-4 text-xs font-black text-gray-400 uppercase">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>