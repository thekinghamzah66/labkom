<x-layouts.app title="Pilih Praktikum">
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-100 w-full max-w-2xl text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Selamat Datang, {{ auth()->user()->name }}</h2>
            <p class="text-gray-500 mb-10">Silakan pilih praktikum yang akan Anda kelola hari ini:</p>
            
            <form action="{{ route('aslab.select-practicum.store') }}" method="POST" class="grid gap-4">
                @csrf
                @forelse($practicums as $p)
                    <button name="practicum_id" value="{{ $p->id }}" 
                        class="group p-6 border-2 border-gray-50 rounded-3xl hover:border-violet-500 hover:bg-violet-50 transition-all duration-300 text-left flex justify-between items-center">
                        <div>
                            <span class="block text-sm text-gray-400 uppercase tracking-widest font-bold mb-1">Mata Kuliah</span>
                            <span class="text-xl font-bold text-gray-800 group-hover:text-violet-700">{{ $p->name }}</span>
                        </div>
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-violet-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </button>
                @empty
                    <div class="p-10 border-2 border-dashed border-gray-100 rounded-3xl">
                        <p class="text-gray-400 font-medium">Anda belum ditugaskan di praktikum manapun.</p>
                    </div>
                @endforelse
            </form>
        </div>
    </div>
</x-layouts.app>