@php
    $role = auth()->user()?->role?->slug ?? '';
    $name = auth()->user()?->name ?? '';

    $menus = [
'kalab' => [
    ['label' => 'Master Dashboard',  'route' => 'kalab.dashboard',         'icon' => 'chart-bar'],
    ['label' => 'Manajemen Matkul',  'route' => 'kalab.practicums.index',  'icon' => 'book-open'], // Menu Baru
    ['label' => 'Kelulusan Final',   'route' => 'kalab.graduation',       'icon' => 'academic-cap'],
    ['label' => 'User Management',   'route' => 'kalab.users',            'icon' => 'users'],
    ['label' => 'Export Report',     'route' => 'kalab.export',           'icon' => 'document-download'],
],
       'mahasiswa' => [
    ['label' => 'Dashboard',  'route' => 'mahasiswa.dashboard',    'icon' => 'home'],
    ['label' => 'Modul',      'route' => 'mahasiswa.modules',      'icon' => 'book'],
    ['label' => 'Tugas',      'route' => 'mahasiswa.submissions',  'icon' => 'document-text'],
    ['label' => 'Progres',    'route' => 'mahasiswa.progress',     'icon' => 'chart-bar'],
    ['label' => 'Scan QR',    'route' => 'mahasiswa.scan',         'icon' => 'qr-code'],
    ['label' => 'Logbook',    'route' => 'mahasiswa.logbook',      'icon' => 'book-open'],
],
       'aslab' => [
    ['label' => 'Dashboard',    'route' => 'aslab.dashboard',     'icon' => 'home'],
    ['label' => 'Data Mahasiswa','route' => 'aslab.mahasiswa',     'icon' => 'users'], // Menu baru
    ['label' => 'Data Sesi',     'route' => 'aslab.sesi',          'icon' => 'calendar'], // Menu baru
    ['label' => 'CMS Materi',   'route' => 'aslab.modules.index', 'icon' => 'book'],
    ['label' => 'Penilaian',    'route' => 'aslab.grading',       'icon' => 'star'],
    ['label' => 'Kehadiran QR', 'route' => 'aslab.attendance',    'icon' => 'check'],
],
'dosen-pembimbing' => [
    ['label' => 'Dashboard',       'route' => 'dosen-pembimbing.dashboard',             'icon' => 'home'],
    ['label' => 'Validasi Soal',    'route' => 'dosen-pembimbing.validation',            'icon' => 'check'],
['label' => 'Penilaian Ujian', 'route' => 'dosen-pembimbing.penilaian-ujian.index', 'icon' => 'clipboard-check'],
    ['label' => 'Logbook',         'route' => 'dosen-pembimbing.logbook',               'icon' => 'book'],
],
    ];

    $colors = [
        'kalab'            => ['bg' => 'bg-violet-600', 'light' => 'bg-violet-50', 'text' => 'text-violet-600', 'border' => 'border-violet-200'],
        'mahasiswa'        => ['bg' => 'bg-sky-500',    'light' => 'bg-sky-50',    'text' => 'text-sky-600',    'border' => 'border-sky-200'],
        'aslab'            => ['bg' => 'bg-emerald-500','light' => 'bg-emerald-50','text' => 'text-emerald-600','border' => 'border-emerald-200'],
        'dosen-pembimbing' => ['bg' => 'bg-amber-500',  'light' => 'bg-amber-50',  'text' => 'text-amber-600',  'border' => 'border-amber-200'],
    ];

    $color   = $colors[$role] ?? $colors['mahasiswa'];
    $navMenu = $menus[$role]  ?? [];
@endphp

{{-- 1. Overlay (Hanya muncul di mobile saat sidebar terbuka) --}}
<div x-show="sidebarOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false" 
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden">
</div>

{{-- 2. Sidebar --}}
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="w-64 bg-white border-r border-gray-100 flex flex-col fixed top-0 left-0 h-full z-40 transition-transform duration-300 ease-in-out lg:translate-x-0">

   {{-- Logo --}}
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center bg-white border border-gray-50 p-1 shadow-sm">
                <img src="{{ asset('img/logo-untag.jpg') }}" alt="Logo Untag" class="w-full h-full object-contain">
            </div>
            <div>
                <p class="font-display font-bold text-gray-900 text-sm leading-tight">Labkom</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Untag Surabaya</p>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- Role Badge --}}
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-2 px-3 py-2 rounded-xl {{ $color['light'] }} {{ $color['border'] }} border">
            <span class="w-2 h-2 rounded-full {{ $color['bg'] }}"></span>
            <span class="text-xs font-semibold {{ $color['text'] }}">{{ auth()->user()?->role?->name }}</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-4 overflow-y-auto">
        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold px-3 mb-2">Menu</p>
        <ul class="space-y-1">
            @foreach ($navMenu as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ $active ? $color['light'].' '.$color['text'].' font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                        <span class="w-5 h-5 flex-shrink-0">
                            @if($item['icon'] === 'home')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            @elseif($item['icon'] === 'chart')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                            @elseif($item['icon'] === 'graduation')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A5.905 5.905 0 018 3.993a5.905 5.905 0 014.28 1.933A5.905 5.905 0 0116.28 3.993a5.905 5.905 0 014.717 5.341c.21.136.421.277.63.413m-15.482 0l6.57 4.027a1.125 1.125 0 001.125 0l6.57-4.027L12 6.106l-7.74 4.041z"/></svg>
                            @elseif($item['icon'] === 'users')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            @elseif($item['icon'] === 'document')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            @elseif($item['icon'] === 'camera')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                            @elseif($item['icon'] === 'book')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            @elseif($item['icon'] === 'star')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                            @elseif($item['icon'] === 'check')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @else
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path d="M12 6v12m6-6H6"/></svg>
                            @endif
                        </span>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- User footer --}}
    <div class="px-4 py-4 border-t border-gray-100">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-8 h-8 rounded-lg {{ $color['bg'] }} flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">{{ strtoupper(substr($name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ $name }}</p>
                <p class="text-[10px] text-gray-400 truncate">{{ auth()->user()?->username }}</p>
            </div>
        </div>
    </div>
</aside>