<x-layouts.app title="Bank Soal Utama">
    {{-- Container Utama dengan Alpine.js untuk handling 2 Modal --}}
    <div class="p-6" x-data="{ openAdd: false, openEdit: false, currExam: {} }">
        
        {{-- 1. NOTIFIKASI: Diperbaiki posisinya agar tidak ngebug --}}
        @if(session('success'))
            <div class="mb-6 animate-fade-in">
                <div class="flex items-center p-4 bg-emerald-50 border border-emerald-100 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-emerald-800 font-black text-sm uppercase">Berhasil!</p>
                        <p class="text-emerald-600 text-xs font-medium">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- 2. Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight text-amber-600">Bank Soal Utama</h1>
                <p class="text-sm text-gray-400 mt-1 font-medium italic">Kelola repositori soal ujian tengah dan akhir praktikum.</p>
            </div>
            <button @click="openAdd = true" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-100 transition-all flex items-center gap-2 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Terbitkan Soal Ujian
            </button>
        </div>

        {{-- 3. Daftar Soal --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($exams as $e)
                <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        
                        {{-- TOMBOL MANAJEMEN: EDIT & HAPUS --}}
                        <div class="flex gap-2">
                            <button @click="openEdit = true; currExam = { 
                                id: '{{ $e->id }}', 
                                title: '{{ $e->title }}', 
                                type: '{{ $e->type }}', 
                                deadline: '{{ $e->deadline ? $e->deadline->format('Y-m-d\TH:i') : '' }}' 
                            }" class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            {{-- Tombol Hapus dengan SweetAlert --}}
<form action="{{ route('dosen-pembimbing.exams.destroy', $e->id) }}" 
      method="POST" 
      id="delete-form-{{ $e->id }}">
    @csrf @method('DELETE')
    <button type="button" 
            onclick="confirmDelete('{{ $e->id }}', '{{ $e->title }}')"
            class="p-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </button>
</form>
                        </div>
                    </div>

                    <div class="inline-block px-3 py-1 bg-amber-600 text-white text-[9px] font-black rounded-lg uppercase tracking-widest mb-3">
                        {{ $e->type }}
                    </div>
                    <h3 class="text-xl font-black text-gray-800 leading-tight">{{ $e->title }}</h3>
                    
                    <div class="mt-8 pt-6 border-t border-gray-50 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Batas Waktu:</p>
                            <p class="text-xs font-bold text-gray-600 mt-0.5">
                                {{ $e->deadline ? $e->deadline->format('d M Y, H:i') : 'TIDAK ADA' }} WIB
                            </p>
                        </div>
                        <a href="{{ asset('storage/' . $e->file_path) }}" target="_blank" class="px-4 py-2 bg-gray-50 text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all">Preview &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold italic">Belum ada soal ujian yang diterbitkan.</p>
                </div>
            @endforelse
        </div>

        {{-- 4. MODAL TAMBAH (Fungsi Store) --}}
        <div x-show="openAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-8 max-w-lg w-full shadow-2xl">
                <h3 class="text-xl font-black text-gray-900 uppercase mb-6">Terbitkan Soal Baru</h3>
                <form action="{{ route('dosen-pembimbing.exams.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Ujian</label>
                        <input type="text" name="title" placeholder="Misal: UAS Praktikum Jaringan" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-amber-500 outline-none" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipe</label>
                            <select name="type" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm outline-none">
                                <option value="UTP">UTP (Tengah)</option>
                                <option value="UAS">UAS (Akhir)</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deadline</label>
                            <input type="datetime-local" name="deadline" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Unggah File Soal</label>
                        <input type="file" name="file_materi" class="mt-2 text-[10px] text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-amber-50 file:text-amber-700" required>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="openAdd = false" class="flex-1 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-amber-100 hover:bg-amber-600 transition-all">Publikasikan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 5. MODAL EDIT (Fungsi Update) --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-8 max-w-lg w-full shadow-2xl border border-emerald-100">
                <h3 class="text-xl font-black text-emerald-600 uppercase mb-6">Edit Data Soal</h3>
                <form :action="'/dosen/exams/' + currExam.id" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Ujian</label>
                        <input type="text" name="title" x-model="currExam.title" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis</label>
                            <select name="type" x-model="currExam.type" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm outline-none">
                                <option value="UTP">UTP</option>
                                <option value="UAS">UAS</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deadline Baru</label>
                            <input type="datetime-local" name="deadline" x-model="currExam.deadline" class="w-full mt-1.5 p-4 bg-gray-50 rounded-2xl border-none text-sm outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ganti File (Opsional)</label>
                        <input type="file" name="file_materi" class="mt-2 text-[10px] text-gray-400">
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="openEdit = false" class="flex-1 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
    // 1. Alert Konfirmasi Hapus yang Keren
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: "Soal '" + title + "' akan dihapus permanen dan mahasiswa tidak bisa mengaksesnya lagi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Merah
            cancelButtonColor: '#9ca3af', // Abu-abu
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-[2.5rem] p-8',
                title: 'font-black uppercase tracking-tight',
                confirmButton: 'rounded-2xl px-6 py-3 font-bold',
                cancelButton: 'rounded-2xl px-6 py-3 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Jalankan submit form jika user klik YA
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // 2. Alert Berhasil (Create/Update/Delete)
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'MANTAP!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-[2.5rem] p-8',
                title: 'font-black text-emerald-600 uppercase tracking-tight',
                timerProgressBar: 'bg-emerald-500'
            }
        });
    @endif
</script>
</x-layouts.app>