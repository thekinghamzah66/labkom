@php
    $role = auth()->user()?->role?->slug ?? '';
    $name = auth()->user()?->name ?? '';

    $menus = [
        'kalab' => [
            ['label' => 'Dashboard',   'route' => 'kalab.dashboard',   'icon' => 'home'],
            ['label' => 'Monitoring',  'route' => 'kalab.monitoring',  'icon' => 'chart'],
            ['label' => 'Jadwal',      'route' => 'kalab.schedules',   'icon' => 'calendar'],
            ['label' => 'Sumber Daya', 'route' => 'kalab.resources',   'icon' => 'cube'],
        ],
        'mahasiswa' => [
            ['label' => 'Dashboard',  'route' => 'mahasiswa.dashboard',    'icon' => 'home'],
            ['label' => 'Modul',      'route' => 'mahasiswa.modules',      'icon' => 'book'],
            ['label' => 'Tugas',      'route' => 'mahasiswa.submissions',  'icon' => 'document'],
            ['label' => 'Progres',    'route' => 'mahasiswa.progress',     'icon' => 'chart'],
        ],
        'aslab' => [
            ['label' => 'Dashboard',      'route' => 'aslab.dashboard',      'icon' => 'home'],
            ['label' => 'Penilaian',      'route' => 'aslab.grading',        'icon' => 'star'],
            ['label' => 'Kehadiran',      'route' => 'aslab.attendance',     'icon' => 'check'],
            ['label' => 'Troubleshoot',   'route' => 'aslab.troubleshooting','icon' => 'wrench'],
        ],
        'dosen-pembimbing' => [
            ['label' => 'Dashboard',   'route' => 'dosen.dashboard',   'icon' => 'home'],
            ['label' => 'Review',      'route' => 'dosen.reviews',     'icon' => 'document'],
            ['label' => 'Monitoring',  'route' => 'dosen.monitoring',  'icon' => 'chart'],
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

<aside class="w-64 bg-white border-r border-gray-100 flex flex-col fixed top-0 left-0 h-full z-20">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl {{ $color['bg'] }} flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-sm">L</span>
            </div>
            <div>
                <p class="font-display font-bold text-gray-900 text-sm leading-tight">Labkom</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">UNTAG Surabaya</p>
            </div>
        </div>
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
                        {{-- Icon --}}
                        <span class="w-5 h-5 flex-shrink-0">
                            @if($item['icon'] === 'home')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            @elseif($item['icon'] === 'chart')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                            @elseif($item['icon'] === 'calendar')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            @elseif($item['icon'] === 'cube')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                            @elseif($item['icon'] === 'book')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            @elseif($item['icon'] === 'document')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            @elseif($item['icon'] === 'star')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            @elseif($item['icon'] === 'check')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($item['icon'] === 'wrench')
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/></svg>
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
