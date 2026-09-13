<x-layouts.app title="Pilih Praktikum Bimbingan">
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-100 w-full max-w-2xl text-center">
            <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2 font-display">Selamat Datang, Dosen!</h2>
            <p class="text-gray-500 mb-10">Silakan pilih mata kuliah praktikum untuk melihat daftar mahasiswa bimbingan Anda:</p>
            
            <form action="{{ route('dosen-pembimbing.select-practicum.store') }}" method="POST" class="grid gap-4">
                @csrf
                @forelse($practicums as $p)
                    <button name="practicum_id" value="{{ $p->id }}" 
                        class="group p-6 border-2 border-gray-50 rounded-3xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-300 text-left flex justify-between items-center">
                        <div>
                            <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-black mb-1">Mata Kuliah</span>
                            <span class="text-xl font-bold text-gray-800 group-hover:text-indigo-700">{{ $p->name }}</span>
                        </div>
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"></path></svg>
                        </div>
                    </button>
                @empty
                    <div class="p-10 border-2 border-dashed border-gray-100 rounded-3xl">
                        <p class="text-gray-400 font-medium font-display italic">Belum ada mahasiswa yang ditugaskan kepada Anda.</p>
                        <p class="text-[10px] text-gray-300 mt-2 uppercase tracking-widest">Pastikan Aslab sudah memilihkan nama Anda sebagai pembimbing di menu Data Sesi.</p>
                    </div>
                @endforelse
            </form>
        </div>
    </div>
</x-layouts.app>