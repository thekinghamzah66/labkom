<x-layouts.app title="Manajemen Praktikum">
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight font-display text-indigo-600">Daftar Mata Kuliah Praktikum</h1>
                <p class="text-sm text-gray-400 mt-1">Kelola semua praktikum dan tugaskan asisten laboratorium di sini.</p>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl animate-fade-in shadow-sm font-bold text-sm mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- 1. FORM TAMBAH PRAKTIKUM --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm sticky top-24">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Tambah Praktikum Baru</h3>
                <form action="{{ route('kalab.practicums.store') }}" method="POST" class="space-y-6">
                    @csrf
                    {{-- Input Nama --}}
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Mata Kuliah</label>
                        <input type="text" name="name" placeholder="Contoh: Rekayasa Perangkat Lunak" required
                            class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>

                    {{-- PERBAIKAN: Checkbox Pilihan Aslab --}}
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Asisten (Aslab)</label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto p-3 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            @forelse($allAslabs as $aslab)
                                <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 cursor-pointer hover:bg-indigo-50 transition-all group">
                                    <input type="checkbox" name="aslab_ids[]" value="{{ $aslab->id }}" 
                                        class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="flex flex-col leading-none">
                                        <span class="text-xs font-bold text-gray-700 group-hover:text-indigo-700">{{ $aslab->name }}</span>
                                        <span class="text-[9px] text-gray-400 mt-1 uppercase">{{ $aslab->username }}</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-[10px] text-gray-400 text-center py-4 italic">Belum ada user Aslab.</p>
                            @endforelse
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Simpan Praktikum
                    </button>
                </form>
            </div>

            {{-- 2. TABEL DAFTAR PRAKTIKUM --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b">
                            <tr>
                                <th class="py-5 px-8">Nama Praktikum</th>
                                <th class="py-5 px-8">Asisten Bertugas</th>
                                <th class="py-5 px-8 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($practicums as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-5 px-8">
                                    <span class="font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $p->name }}</span>
                                    <p class="text-[9px] text-gray-400 mt-1 uppercase">Dibuat: {{ $p->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="py-5 px-8">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($p->users as $u)
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase border border-indigo-100">{{ $u->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-5 px-8">
                                    <div class="flex justify-center">
                                        <form action="{{ route('kalab.practicums.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus praktikum ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-300 hover:text-red-600 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-20 text-center text-gray-400 italic">Belum ada praktikum.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>