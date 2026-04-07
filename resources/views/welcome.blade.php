<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — UNTAG Surabaya</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'display': ['Sora', 'sans-serif'],
                        'body': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3 { font-family: 'Sora', sans-serif; }
        
        /* Container untuk slide agar bisa bergeser ke samping */
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
            transition: opacity 0.5s;
        }
    </style>
</head>

<body class="min-h-screen bg-[#E2EE6B] flex flex-col items-center justify-between p-6 overflow-hidden">

    <div class="w-full text-center pt-4">
        <div class="flex justify-center mb-4">
            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/2/2d/Logo_Untag_Surabaya.png/200px-Logo_Untag_Surabaya.png" alt="Logo UNTAG" class="w-20 h-20 object-contain">
        </div>
        <h1 class="text-[#333] text-2xl font-extrabold tracking-tight leading-tight uppercase">Selamat Datang</h1>
        <h2 class="text-[#333] text-xl font-extrabold uppercase">Di Presensi Online</h2>
        
        <p class="text-[#D35400] font-bold text-lg mt-10">Login sebagai?</p>
    </div>

    <div class="relative flex-1 flex flex-col items-center justify-center w-full overflow-hidden">
        <div id="carousel-track">
            @foreach ($roles as $index => $role)
                <div class="slide-item">
                    <img src="{{ asset('images/roles/' . $role->slug . '.png') }}" 
                         alt="{{ $role->name }}" 
                         class="h-72 object-contain drop-shadow-xl"
                         onerror="this.src='https://via.placeholder.com/300x400?text=Karakter+3D'">
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-full flex flex-col items-center gap-6 pb-10">
        <div class="flex justify-center gap-2" id="dots-container">
            @foreach ($roles as $index => $role)
                <div class="dot w-3 h-3 rounded-full bg-[#F39C12]/30 transition-all duration-300" data-index="{{ $index }}"></div>
            @endforeach
        </div>
        
        <a id="login-link" href="#" 
           class="w-64 py-4 rounded-3xl font-display font-bold text-center text-white bg-[#F39C12] shadow-[0_8px_0_#D35400] active:shadow-none active:translate-y-1 transition-all text-xl">
            <span id="label-role">...</span>
        </a>
    </div>

    <script>
        const roles = @json($roles);
        const track = document.getElementById('carousel-track');
        const dots = document.querySelectorAll('.dot');
        const labelRole = document.getElementById('label-role');
        const loginLink = document.getElementById('login-link');
        
        let currentIdx = 0;
        let startX = 0;
        let isDragging = false;

        function updateUI() {
            // 1. Geser Track Carousel (Transform X)
            track.style.transform = `translateX(-${currentIdx * 100}%)`;

            // 2. Update Dots
            dots.forEach((dot, i) => {
                if(i === currentIdx) {
                    dot.classList.add('bg-[#E74C3C]', 'w-5'); // Warna merah/oranye saat aktif
                    dot.classList.remove('bg-[#F39C12]/30');
                } else {
                    dot.classList.remove('bg-[#E74C3C]', 'w-5');
                    dot.classList.add('bg-[#F39C12]/30');
                }
            });

            // 3. Update Text Button & Link
            const activeRole = roles[currentIdx];
            labelRole.textContent = activeRole.name;
            loginLink.href = `/login/${activeRole.slug}`;
        }

        // Fungsi Swipe (Opsional tapi keren untuk HP)
        track.addEventListener('touchstart', e => startX = e.touches[0].clientX);
        track.addEventListener('touchend', e => {
            const endX = e.changedTouches[0].clientX;
            if (startX - endX > 50 && currentIdx < roles.length - 1) currentIdx++;
            if (startX - endX < -50 && currentIdx > 0) currentIdx--;
            updateUI();
        });

        // Klik pada dot untuk pindah
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                currentIdx = parseInt(dot.dataset.index);
                updateUI();
            });
        });

        // Jalankan saat pertama load
        updateUI();
    </script>

</body>
</html>