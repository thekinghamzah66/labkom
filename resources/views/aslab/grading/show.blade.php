<x-layouts.app title="Beri Nilai & Feedback">
    <div class="max-w-5xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Info Mahasiswa & File --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Detail Mahasiswa</h3>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center font-black">
                            {{ substr($submission->student_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $submission->student_name }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $submission->username }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <p class="text-xs text-gray-500"><span class="font-bold text-gray-900">Modul:</span> {{ $submission->module_title }}</p>
                        <a href="{{ $submission->file_url }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-sky-50 text-sky-600 rounded-2xl text-[10px] font-black uppercase border border-sky-100 hover:bg-sky-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Jawaban
                        </a>
                    </div>
                </div>
            </div>

            {{-- Form Penilaian --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <h3 class="text-xl font-black text-gray-900 uppercase mb-6">Input Nilai & Feedback</h3>
                    
                    <form action="{{ route('aslab.grading.update', $submission->id) }}" method="POST" class="space-y-6">
                        @csrf @method('PATCH')
                        
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Skor Akhir (0-100)</label>
                            <input type="number" name="score" placeholder="Contoh: 85" class="w-full mt-2 p-4 bg-gray-50 rounded-2xl border-none text-2xl font-black text-emerald-600 focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Koreksi / Feedback untuk Mahasiswa</label>
                            <textarea name="feedback" rows="5" placeholder="Berikan catatan perbaikan agar mahasiswa belajar dari kesalahannya..." class="w-full mt-2 p-4 bg-gray-50 rounded-2xl border-none text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <a href="{{ route('aslab.grading') }}" class="flex-1 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Batal</a>
                            <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">
                                Simpan Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>