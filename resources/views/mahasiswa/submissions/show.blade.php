<x-layouts.app title="Detail Tugas & Kelulusan">
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- 1. Notifikasi --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                <div class="w-9 h-9 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-emerald-800 font-bold text-sm">Berhasil!</p>
                    <p class="text-emerald-600 text-xs">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                <div class="w-9 h-9 bg-rose-500 text-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div>
                    <p class="text-rose-800 font-bold text-sm">Terjadi Kesalahan!</p>
                    <p class="text-rose-600 text-xs">{{ session('error') ?? $errors->first() }}</p>
                </div>
            </div>
        @endif

        {{-- 2. Card Utama --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Header Card --}}
            <div class="p-8 border-b border-gray-50 bg-gradient-to-r from-white to-sky-50/30">
                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <span class="px-3 py-1 bg-sky-50 text-sky-600 text-[10px] font-black uppercase rounded-lg tracking-widest border border-sky-100">Lembar Tugas</span>
                        <h2 class="text-2xl font-black text-gray-900 mt-3 leading-tight font-display">{{ $module->title }}</h2>
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Batas Pengumpulan: {{ \Carbon\Carbon::parse($module->deadline)->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm min-w-[140px] text-center">
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Sisa Waktu</p>
                        <div id="countdown" class="text-lg font-black text-red-500 tabular-nums">00:00:00</div>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-8">
                {{-- STATUS LOGIC BERDASARKAN HASIL ASLAB --}}
                @if($module->userSubmission)
                    @php $status = $module->userSubmission->status; @endphp

                    {{-- --- KONDISI A: REMIDI ASLAB --- --}}
                    @if($status == 'Remidi')
                        <div class="bg-amber-50 border-2 border-dashed border-amber-200 rounded-[2.5rem] p-10 text-center animate-fade-in">
                            <h3 class="text-2xl font-black text-amber-900 uppercase tracking-tighter">Tahap Remidi Praktikum</h3>
                            <p class="text-amber-700/70 text-sm mt-2 mb-6">Asisten Lab meminta perbaikan pada tugas Anda. Silahkan unduh soal remidi dan kirim ulang.</p>
                            
                            @if($module->file_remidi)
                                <a href="{{ asset('storage/' . $module->file_remidi) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-amber-200 rounded-xl text-amber-600 font-bold text-xs uppercase mb-6 shadow-sm hover:bg-amber-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Unduh Soal Remidi
                                </a>
                            @endif

                            <form action="{{ route('mahasiswa.submissions.submit') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3">
                                @csrf
                                <input type="hidden" name="module_id" value="{{ $module->id }}">
                                <input type="file" name="file_tugas" required class="flex-1 bg-white p-3 rounded-xl border border-amber-200 text-xs">
                                <button type="submit" class="bg-amber-500 text-white px-8 py-3 rounded-xl font-black text-xs uppercase shadow-lg hover:bg-amber-600 transition-all">Submit Remidi</button>
                            </form>
                        </div>

                    {{-- --- KONDISI B: LULUS ASLAB (TAHAP DOSBIM) --- --}}
                    @elseif($status == 'Lulus')
                        <div class="bg-emerald-50 border-2 border-emerald-100 rounded-[2.5rem] p-10 space-y-8 animate-fade-in">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-emerald-500 text-white rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-100">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h3 class="text-3xl font-black text-emerald-900 tracking-tighter uppercase font-display leading-none">Anda Lulus Praktikum!</h3>
                                <p class="text-emerald-700/70 text-sm mt-2 font-medium">Lanjutkan bimbingan dan ujian akhir bersama Dosen Pembimbing.</p>
                            </div>

                            <div class="bg-white rounded-3xl p-8 border border-emerald-100 shadow-sm">
                                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-5">Informasi Pembimbing & Ujian Akhir</p>
                                
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center font-black text-emerald-600 border border-emerald-50 text-2xl shadow-inner">
                                        {{ $enrollment && $enrollment->dosbim ? strtoupper(substr($enrollment->dosbim->name, 0, 1)) : '?' }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase leading-none">Dosen Pembimbing:</p>
                                        <p class="text-lg font-black text-gray-800 mt-1 font-display">{{ $enrollment->dosbim->name ?? 'Dosen Belum Ditugaskan' }}</p>
                                    </div>
                                </div>

                                {{-- --- LOGIKA STATUS UJIAN DOSBIM --- --}}
                                @if($enrollment && $enrollment->status_dosbim == 'lulus')
                                    {{-- LULUS FINAL --}}
                                    <div class="p-6 bg-emerald-600 text-white rounded-[2rem] flex flex-col items-center text-center gap-2 shadow-xl shadow-emerald-100 animate-fade-in border-4 border-emerald-200">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <h4 class="text-xl font-black uppercase tracking-widest leading-none">Lulus Final</h4>
                                        <p class="text-xs text-emerald-100 font-bold">Selamat! Anda telah menyelesaikan seluruh rangkaian praktikum.</p>
                                    </div>

                                @elseif($enrollment && $enrollment->status_dosbim == 'remidi')
                                    {{-- REMIDI DOSBIM --}}
                                    <div class="p-6 bg-amber-500 text-white rounded-[2rem] flex flex-col items-center text-center gap-2 shadow-xl shadow-amber-100 animate-fade-in">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <h4 class="text-xl font-black uppercase tracking-widest leading-none">Remidi Ujian Dosbim</h4>
                                        <p class="text-xs text-amber-50 font-bold">Ujian bimbingan Anda perlu diperbaiki. Hubungi Dosen Pembimbing.</p>
                                    </div>

                                @elseif($enrollment && $enrollment->status_dosbim == 'gagal')
                                    {{-- GAGAL FINAL --}}
                                    <div class="p-6 bg-rose-600 text-white rounded-[2rem] flex flex-col items-center text-center gap-2 shadow-xl shadow-rose-100 animate-fade-in">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <h4 class="text-xl font-black uppercase tracking-widest leading-none">Gagal Final</h4>
                                        <p class="text-xs text-rose-50 font-bold italic">Tetap semangat, jangan menyerah!</p>
                                    </div>

                                @elseif($enrollment && $enrollment->soal_ujian_dosbim)
                                    {{-- TAHAP PENGERJAAN SOAL DOSBIM --}}
                                    <div class="space-y-6 pt-6 border-t border-emerald-50">
                                        <a href="{{ asset('storage/' . $enrollment->soal_ujian_dosbim) }}" target="_blank"
                                           class="w-full py-4 bg-sky-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3 shadow-lg hover:bg-sky-700 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            Unduh Soal Ujian Dosbim
                                        </a>

                                        @if(!$enrollment->jawaban_ujian_dosbim)
                                            <div class="bg-gray-50 p-6 rounded-[2rem] border border-gray-100">
                                                <form action="{{ route('mahasiswa.submissions.submit-dosbim') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block ml-1 text-center">Kirim Hasil Ujian Bimbingan</label>
                                                    <div class="flex flex-col gap-3">
                                                        <input type="file" name="file_jawaban" required class="w-full text-xs text-gray-500 bg-white p-3 rounded-xl border border-gray-200">
                                                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md">
                                                            Submit Jawaban ke Dosbim
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <div class="p-6 bg-emerald-500 text-white rounded-[2rem] flex flex-col items-center gap-1 shadow-lg animate-fade-in">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                                <span class="text-xs font-black uppercase tracking-widest">Jawaban Ujian Terkirim</span>
                                            </div>
                                            <p class="text-center text-[10px] text-gray-400 italic">Menunggu Dosen Pembimbing memberikan penilaian akhir.</p>
                                        @endif
                                    </div>
                                @else
                                    {{-- DOSBIM BELUM KASIH SOAL --}}
                                    <div class="mt-6 p-6 bg-gray-50 rounded-[2rem] text-center border border-dashed border-gray-200">
                                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 italic uppercase">Menunggu Soal Ujian diunggah oleh Dosen Pembimbing...</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    {{-- --- KONDISI C: GAGAL ASLAB --- --}}
                    @elseif($status == 'Gagal')
                        <div class="bg-rose-50 border-2 border-rose-100 rounded-[2.5rem] p-12 text-center space-y-6 animate-fade-in">
                            <div class="w-20 h-20 bg-rose-500 text-white rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-rose-100">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h3 class="text-3xl font-black text-rose-900 tracking-tighter uppercase font-display leading-none">Tetap Semangat!</h3>
                            <p class="text-rose-700/70 text-sm max-w-md mx-auto leading-relaxed font-medium italic">
                                "Jangan biarkan kegagalan ini menghentikan langkahmu. Setiap ahli pernah menjadi pemula, dan setiap kegagalan adalah pelajaran berharga untuk sukses di masa depan."
                            </p>
                            <div class="pt-4 flex justify-center gap-2">
                                <div class="w-2 h-2 bg-rose-200 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-rose-300 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                <div class="w-2 h-2 bg-rose-200 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                            </div>
                        </div>

                    {{-- --- KONDISI D: PENDING (PROSES REVIEW ASLAB) --- --}}
                    @else
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-[2.5rem] p-10 text-center animate-fade-in">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-sky-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-500 uppercase tracking-tight">Tugas Dalam Peninjauan</h3>
                            <p class="text-gray-400 text-xs mt-1">Jawaban Anda sudah masuk. Status kelulusan praktikum akan muncul setelah direview oleh Asisten Lab.</p>
                        </div>
                    @endif

                @else
                    {{-- --- TAMPILAN AWAL: BELUM KIRIM TUGAS SAMA SEKALI --- --}}
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-6">
                        <h4 class="text-sm font-black text-gray-900 uppercase mb-2 font-display">Instruksi Pengerjaan:</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $module->description ?? 'Silahkan gunakan file modul yang sudah diunggah sebagai panduan pengerjaan praktikum Anda.' }}</p>
                    </div>

                    <form action="{{ route('mahasiswa.submissions.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                        <div class="relative group" x-data="{ fileName: '' }">
                            <input type="file" name="file_tugas" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="fileName = $event.target.files[0].name" required>
                            <div class="border-2 border-dashed border-gray-200 rounded-[2rem] p-12 text-center group-hover:border-sky-400 group-hover:bg-sky-50/50 transition-all duration-300 shadow-inner bg-gray-50/30">
                                <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <p class="text-base font-bold text-gray-700 font-display" x-text="fileName ? fileName : 'Pilih file jawaban praktikum'"></p>
                                <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-widest">PDF, ZIP, RAR (Maks. 100MB)</p>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-sky-100 transition-all active:scale-[0.98] font-display">
                            Kirim Jawaban Sekarang
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Script Countdown --}}
    <script>
        function updateTimer() {
            const deadlineString = "{{ $module->deadline }}";
            if (!deadlineString) return;
            const deadline = new Date(deadlineString).getTime();
            const now = new Date().getTime();
            const distance = deadline - now;
            if (distance < 0) { document.getElementById("countdown").innerHTML = "WAKTU HABIS"; return; }
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById("countdown").innerHTML = (hours < 10 ? "0"+hours : hours) + ":" + (minutes < 10 ? "0"+minutes : minutes) + ":" + (seconds < 10 ? "0"+seconds : seconds);
        }
        setInterval(updateTimer, 1000); updateTimer();
    </script>
</x-layouts.app>