<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Labkom UNTAG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Tambahkan Alpine.js untuk kontrol Sidebar --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display, h1, h2, h3 { font-family: 'Sora', sans-serif; }
        /* Mengatasi scrollbar liar saat sidebar terbuka di mobile */
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

{{-- 1. Tambahkan x-data di body agar semua komponen bisa akses status sidebarOpen --}}
<body class="bg-gray-50 h-full" x-data="{ sidebarOpen: false }">
    <div class="flex h-full min-h-screen">

        {{-- Sidebar --}}
        {{-- Pastikan di dalam x-sidebar ada logika @click="sidebarOpen = false" pada tombol X --}}
        <x-sidebar />

        {{-- 2. Main area --}}
        {{-- Perubahan: ml-64 diubah menjadi lg:ml-64 agar di mobile kontennya full screen --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-64 transition-all duration-300">

            {{-- Topbar --}}
            {{-- Perubahan: Padding disesuaikan (px-4 di mobile, px-8 di desktop) --}}
            <header class="bg-white border-b border-gray-100 px-4 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    {{-- 3. TOMBOL HAMBURGER (Hanya muncul di mobile) --}}
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div>
                        <h1 class="font-display text-lg lg:text-xl font-bold text-gray-900 leading-none lg:leading-normal">{{ $title ?? 'Dashboard' }}</h1>
                        <p class="text-[10px] lg:text-xs text-gray-400 mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 lg:gap-4">
                    {{-- Notifikasi (Disembunyikan di layar sangat kecil agar tidak sempit) --}}
                    <button class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors hidden sm:block">
                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    {{-- User info --}}
                    <div class="flex items-center gap-3 lg:pl-4 lg:border-l border-gray-100">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-semibold text-gray-800 leading-none">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">{{ auth()->user()->role->name }}</p>
                        </div>
                        <div class="w-8 h-8 lg:w-9 lg:h-9 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center shadow-sm flex-shrink-0">
                            <span class="text-white text-xs lg:text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="Keluar">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page content --}}
            {{-- Perubahan: Padding dinamis (p-4 di mobile, p-8 di desktop) --}}
            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

   {{-- Letakkan ini di bagian paling bawah sebelum tag </body> --}}
<div class="fixed top-5 right-5 z-[9999] space-y-4 w-full max-w-sm pointer-events-none">
    
    {{-- Notif Sukses --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-8"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-8"
         class="pointer-events-auto bg-white border-l-4 border-emerald-500 shadow-2xl rounded-2xl p-4 flex items-center gap-4 border border-gray-100">
        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="flex-1">
            <p class="text-xs font-extrabold text-emerald-800 uppercase tracking-widest">Berhasil</p>
            <p class="text-sm text-gray-600 font-semibold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-gray-300 hover:text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

    {{-- Notif Error --}}
    @if(session('error') || $errors->any())
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-8"
         class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 flex items-center gap-4 border border-gray-100">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <div class="flex-1">
            <p class="text-xs font-extrabold text-red-800 uppercase tracking-widest">Terjadi Kesalahan</p>
            <p class="text-sm text-gray-600 font-semibold">{{ session('error') ?? $errors->first() }}</p>
        </div>
        <button @click="show = false" class="text-gray-300 hover:text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'BERHASIL!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000,
        customClass: { popup: 'rounded-[2rem]' }
    });
</script>
@endif

@if(session('error') || $errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'OOPS!',
        text: "{{ session('error') ?? $errors->first() }}",
        confirmButtonColor: '#ef4444',
        customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl' }
    });
</script>
@endif
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>