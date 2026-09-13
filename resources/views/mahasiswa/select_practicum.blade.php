<x-layouts.app title="Pilih Praktikum Anda">
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-100 w-full max-w-2xl text-center">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2 text-display">Halo, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-500 mb-10">Pilih praktikum yang ingin Anda akses sekarang:</p>
            
            <form action="{{ route('mahasiswa.select-practicum.store') }}" method="POST" class="grid gap-4">
                @csrf
                @forelse($practicums as $p)
                    <button name="practicum_id" value="{{ $p->id }}" 
                        class="group p-6 border-2 border-gray-50 rounded-3xl hover:border-emerald-500 hover:bg-emerald-50 transition-all duration-300 text-left flex justify-between items-center">
                        <div>
                            <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Status: Terdaftar</span>
                            <span class="text-xl font-bold text-gray-800 group-hover:text-emerald-700">{{ $p->name }}</span>
                        </div>
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </button>
                @empty
                    <div class="p-10 border-2 border-dashed border-gray-100 rounded-3xl">
                        <p class="text-gray-400 font-medium font-display">Belum ada praktikum yang tersedia.</p>
                    </div>
                @endforelse
            </form>
        </div>
    </div>
</x-layouts.app>