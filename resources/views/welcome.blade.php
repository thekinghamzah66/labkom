<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — UNTAG Surabaya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3 { font-family: 'Sora', sans-serif; }

        #carousel-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            width: 100%;
        }
        .slide-item {
            flex: 0 0 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dot styling pakai CSS biasa, bukan Tailwind dinamis */
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 9999px;
            background-color: rgba(243, 156, 18, 0.3);
            transition: all 0.3s;
            cursor: pointer;
        }
        .dot.active {
            width: 20px;
            background-color: #E74C3C;
        }

        /* Fallback card kalau gambar tidak ada */
        .role-fallback {
            width: 192px;
            height: 288px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 1.5rem;
            background: rgba(255,255,255,0.6);
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            gap: 12px;
        }
        .role-fallback .emoji { font-size: 4rem; }
        .role-fallback .label {
            font-weight: 700;
            color: #374151;
            text-align: center;
            padding: 0 16px;
            font-size: 1rem;
        }
    </style>
</head>

<body class="min-h-screen bg-[#E2EE6B] flex flex-col items-center justify-center p-6 overflow-hidden">

    {{-- Header --}}
    <div class="w-full text-center pt-4">
        <div class="flex justify-center mb-4">
            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/2/2d/Logo_Untag_Surabaya.png/200px-Logo_Untag_Surabaya.png"
                 alt="Logo UNTAG"
                 class="w-20 h-20 object-contain"
                 onerror="this.onerror=null; this.style.display='none';">
        </div>
        <h1 class="text-[#333] text-2xl font-extrabold tracking-tight leading-tight uppercase">Selamat Datang</h1>
        <h2 class="text-[#333] text-xl font-extrabold uppercase">Di Presensi Online</h2>
        <p class="text-[#D35400] font-bold text-lg mt-10">Login sebagai?</p>
    </div>

    {{-- Carousel --}}
    <div class="relative flex-1 flex flex-col items-center justify-center w-full overflow-hidden">
        <div id="carousel-track">
            @forelse ($roles as $index => $role)
                <div class="slide-item">
                    {{-- Gambar role dengan fallback aman (tidak loop) --}}
                    <img src="{{ asset('images/roles/' . $role->slug . '.png') }}"
                         alt="{{ $role->name }}"
                         class="h-72 object-contain drop-shadow-xl"
                         onerror="
                             this.onerror=null;
                             this.style.display='none';
                             this.nextElementSibling.style.display='flex';
                         ">

                    {{-- Fallback card (tersembunyi, muncul kalau gambar 404) --}}
                    <div class="role-fallback" style="display:none;">
                        <span class="emoji">
                            @switch($role->slug)
                                @case('kalab') 🛡️ @break
                                @case('mahasiswa') 🎓 @break
                                @case('aslab') 🧪 @break
                                @case('dosen-pembimbing') 💼 @break
                                @default 👤
                            @endswitch
                        </span>
                        <span class="label">{{ $role->name }}</span>
                    </div>
                </div>
            @empty
                <div class="slide-item">
                    <div class="role-fallback">
                        <span class="emoji">⚠️</span>
                        <span class="label">Belum ada role tersedia</span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Footer: Dots + Tombol + Badge --}}
    <div class="w-full flex flex-col items-center gap-6 pb-10">

        {{-- Dots --}}
        <div class="flex justify-center gap-2" id="dots-container">
            @foreach ($roles as $index => $role)
                <div class="dot" data-index="{{ $index }}"></div>
            @endforeach
        </div>

        @if ($roles->isNotEmpty())
            {{-- Tombol Login --}}
            <a id="login-link"
               href="{{ url('/login/' . $roles->first()->slug) }}"
               class="w-64 py-4 rounded-3xl font-bold text-center text-white bg-[#F39C12] shadow-[0_8px_0_#D35400] active:shadow-none active:translate-y-1 transition-all text-xl">
                <span id="label-role">{{ $roles->first()->name }}</span>
            </a>

            {{-- Badge role --}}
            <div class="flex flex-wrap justify-center gap-2 text-sm text-gray-800">
                @foreach ($roles as $role)
                    <span class="px-3 py-1 rounded-full bg-white/80 text-gray-900 shadow-sm">
                        {{ $role->name }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-700">Role belum tersedia. Silakan hubungi administrator.</p>
        @endif
    </div>

    <script>
        // Ambil hanya field yang dibutuhkan, hindari karakter aneh dari field lain
        const roles = @json($rolesJson);


        const track     = document.getElementById('carousel-track');
        const dots      = document.querySelectorAll('.dot');
        const labelRole = document.getElementById('label-role');
        const loginLink = document.getElementById('login-link');

        let currentIdx = 0;
        let startX     = 0;

        function updateUI() {
            if (!roles.length) return;

            // 1. Geser carousel
            track.style.transform = `translateX(-${currentIdx * 100}%)`;

            // 2. Update dots (pakai CSS class biasa, bukan Tailwind arbitrary)
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIdx);
            });

            // 3. Update tombol
            const activeRole    = roles[currentIdx];
            labelRole.textContent = activeRole.name;
            loginLink.href        = `/login/${activeRole.slug}`;
        }

        // Swipe support untuk mobile
        track.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
        });

        track.addEventListener('touchend', e => {
            const diff = startX - e.changedTouches[0].clientX;
            if (diff > 50 && currentIdx < roles.length - 1) currentIdx++;
            if (diff < -50 && currentIdx > 0) currentIdx--;
            updateUI();
        });

        // Klik dot
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                currentIdx = parseInt(dot.dataset.index);
                updateUI();
            });
        });

        // Init
        updateUI();
    </script>

</body>
</html>
