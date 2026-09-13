<x-layouts.app title="Data Sesi Praktikum">
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 text-display">Rekapitulasi Sesi & Dosbim</h2>
                <p class="text-gray-500 text-sm">Kelola jadwal sesi dan tentukan Dosen Pembimbing untuk mahasiswa yang lulus.</p>
            </div>
        </div>

        @forelse($sessions as $sessionName => $students)
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header Sesi --}}
            <div class="bg-violet-50 px-8 py-5 border-b border-violet-100 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-violet-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-violet-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg leading-none">{{ $sessionName }}</h3>
                        <p class="text-[11px] text-violet-600 font-extrabold uppercase tracking-widest mt-1.5">
                            Pukul {{ $students->first()->jam }} — Ruang {{ $students->first()->ruangan }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-white text-violet-600 px-4 py-1.5 rounded-xl text-xs font-bold border border-violet-200 shadow-sm">
                        {{ $students->count() }} Mahasiswa
                    </span>
                </div>
            </div>
            
            {{-- Grid Mahasiswa --}}
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($students as $item)
                <div class="flex flex-col p-5 border border-gray-100 rounded-[2rem] hover:border-violet-200 hover:shadow-md hover:shadow-gray-100 transition-all bg-white group">
                    {{-- Info Mahasiswa --}}
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 bg-gray-50 text-gray-400 group-hover:bg-violet-600 group-hover:text-white rounded-2xl flex items-center justify-center font-bold text-lg transition-colors shadow-inner">
                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-sm truncate leading-tight">{{ $item->user->name }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-semibold uppercase tracking-wider">{{ $item->user->username }}</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-dashed border-gray-100">
                        
                        {{-- STATUS DISPLAY (Hanya Menampilkan, Tidak Bisa Input) --}}
                        <div class="mb-4 flex items-center justify-between">
                            @if($item->status_aslab == 'lulus')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-bold uppercase">Lulus</span>
                            @elseif($item->status_aslab == 'remidi')
                                <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-lg text-[10px] font-bold uppercase">Remidi</span>
                            @elseif($item->status_aslab == 'gagal')
                                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-[10px] font-bold uppercase">Gagal</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-[10px] font-bold uppercase">Belum Dinilai</span>
                            @endif

                            <span class="text-xs font-bold text-gray-700">
                                Nilai: {{ $item->nilai_remidi ?? ($item->nilai_awal ?? '-') }}
                            </span>
                        </div>

                        {{-- LOGIKA 1: JIKA STATUS LULUS -> BOLEH PILIH DOSBIM --}}
                        @if($item->status_aslab == 'lulus')
                            <form action="{{ route('aslab.mahasiswa.assign-dosbim', $item->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest ml-1">Dosen Pembimbing</label>
                                    <div class="flex gap-2">
                                        <select name="dosbim_id" class="flex-1 text-xs font-semibold bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-violet-500 py-2.5 px-3 appearance-none">
                                            <option value="">Pilih Dosbim...</option>
                                            @foreach($listDosbim as $dosen)
                                                <option value="{{ $dosen->id }}" {{ $item->dosbim_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-violet-600 text-white p-2.5 rounded-xl hover:bg-violet-700 transition-all shadow-lg shadow-violet-100" title="Simpan Dosbim">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @if($item->dosbim_id)
                                <div class="mt-3 flex items-center gap-2 px-3 py-2 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <span class="text-[10px] font-bold text-emerald-700 truncate italic">Terpilih: {{ $item->dosbim->name }}</span>
                                </div>
                            @endif

                        {{-- LOGIKA 2: JIKA BELUM LULUS --}}
                        @else
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Menunggu Kelulusan</p>
                                <p class="text-[9px] text-gray-400 mt-2 font-medium">Beri nilai di menu <span class="font-bold text-violet-500">Penilaian</span> untuk membuka fitur Dosbim.</p>
                            </div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-white p-24 rounded-[3rem] border-2 border-dashed border-gray-100 text-center shadow-inner">
            <h3 class="text-xl font-bold text-gray-400 uppercase tracking-widest font-display">Belum Ada Sesi</h3>
            <p class="text-gray-300 text-sm mt-2">Silakan plotting sesi di menu Data Mahasiswa terlebih dahulu.</p>
        </div>
        @endforelse
    </div>
</x-layouts.app>