<x-layouts.app title="Presensi QR Digital">
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Bagian Kiri: QR CODE --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm text-center">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-lg tracking-widest">Scanner Area</span>
                    <h2 class="text-xl font-black text-gray-900 mt-4 uppercase">QR Presensi</h2>
                    <p class="text-xs text-gray-400 mt-1 mb-8">Tampilkan kode ini di proyektor</p>

                    {{-- QR Code Generator (Menggunakan QuickChart API) --}}
                    <div class="bg-emerald-50 p-6 rounded-[2rem] border-4 border-white shadow-inner mb-6">
                        <img src="https://quickchart.io/qr?text={{ $todayToken }}&size=300&ecLevel=H" 
                             alt="QR Code Presensi" 
                             class="w-full h-auto rounded-xl shadow-sm">
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Token Sesi:</p>
                        <p class="text-sm font-mono font-bold text-emerald-700 mt-1">{{ $todayToken }}</p>
                    </div>
                    
                    <button onclick="window.location.reload()" class="mt-6 text-[10px] font-black text-emerald-600 hover:underline uppercase tracking-widest">
                        Refresh Token
                    </button>
                </div>
            </div>

            {{-- Bagian Kanan: List Mahasiswa Hadir --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden h-full">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-emerald-900 text-white">
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tight">Mahasiswa Hadir</h3>
                            <p class="text-xs text-emerald-300 font-medium italic">Sesi Praktikum Hari Ini: {{ date('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black">{{ $attendees->count() }}</span>
                            <p class="text-[10px] uppercase font-bold text-emerald-400">Total</p>
                        </div>
                    </div>

                    <div class="overflow-y-auto max-h-[500px]">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <tr>
                                    <th class="py-4 px-8">Informasi Mahasiswa</th>
                                    <th class="py-4 px-8">Waktu Scan</th>
                                    <th class="py-4 px-8 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($attendees as $a)
                                <tr class="animate-fade-in">
                                    <td class="py-5 px-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center font-black text-xs">
                                                {{ substr($a->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm">{{ $a->user->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-mono">{{ $a->user->username }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-8 text-[10px] text-gray-400 font-black">
                                        {{ $a->created_at->format('H:i:s') }} WIB
                                    </td>
                                    <td class="py-5 px-8 text-center">
                                        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">
                                            HADIR
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-20 text-center">
                                        <div class="flex flex-col items-center opacity-20">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            <p class="text-sm font-black uppercase tracking-widest">Belum ada scan masuk</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>