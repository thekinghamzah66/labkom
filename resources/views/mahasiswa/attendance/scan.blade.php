<x-layouts.app title="Scan Presensi">
    <div class="max-w-xl mx-auto p-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm text-center">
            <span class="px-3 py-1 bg-sky-50 text-sky-600 text-[10px] font-black uppercase rounded-lg tracking-widest">Digital Scanner</span>
            <h2 class="text-2xl font-black text-gray-900 mt-4 uppercase">Arahkan Kamera</h2>
            <p class="text-xs text-gray-400 mt-1 mb-8">Scan QR Code yang ditampilkan oleh Aslab di depan kelas</p>

            {{-- Area Kamera --}}
            <div id="reader" class="overflow-hidden rounded-3xl border-4 border-gray-50 bg-gray-50 shadow-inner mb-6"></div>

            <div id="result" class="hidden animate-bounce p-4 bg-emerald-50 text-emerald-600 rounded-2xl font-bold text-sm mb-4">
                QR Terdeteksi! Memproses...
            </div>

            <a href="{{ route('mahasiswa.dashboard') }}" class="text-[10px] font-black text-gray-400 hover:text-sky-600 uppercase tracking-widest transition-colors">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Import Library HTML5 QR Code --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Matikan scanner agar tidak scan berulang kali saat proses kirim
        html5QrcodeScanner.clear();
        
        // Tampilkan loading popup
        Swal.fire({
            title: 'Memproses Presensi...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            },
            customClass: {
                popup: 'rounded-[2rem]',
            }
        });

        // Kirim data ke server lewat AJAX
        fetch("{{ route('mahasiswa.attendance.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Notifikasi BERHASIL yang cakep
                Swal.fire({
                    icon: 'success',
                    title: 'PRESENSI BERHASIL!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-[2rem]',
                        title: 'font-black text-emerald-600 uppercase tracking-tight',
                        timerProgressBar: 'bg-emerald-500'
                    }
                }).then(() => {
                    window.location.href = "{{ route('mahasiswa.dashboard') }}";
                });
            } else {
                // Notifikasi GAGAL (QR salah/sudah absen)
                Swal.fire({
                    icon: 'error',
                    title: 'PRESENSI GAGAL',
                    text: data.message,
                    confirmButtonText: 'COBA LAGI',
                    confirmButtonColor: '#0ea5e9', // Warna Sky Blue
                    customClass: {
                        popup: 'rounded-[2rem]',
                        title: 'font-black text-red-600 uppercase tracking-tight',
                        confirmButton: 'rounded-xl font-bold px-8 py-3'
                    }
                }).then(() => {
                    location.reload(); // Refresh untuk scan ulang
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: 'warning',
                title: 'GANGGUAN KONEKSI',
                text: 'Gagal terhubung ke server. Pastikan internet kamu aktif.',
                confirmButtonText: 'OKE',
                confirmButtonColor: '#0ea5e9',
                customClass: {
                    popup: 'rounded-[2rem]',
                    title: 'font-black text-amber-600 uppercase tracking-tight'
                }
            });
        });
    }

    // Inisialisasi Scanner
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { 
            fps: 15, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0 
        }
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>

    <style>
        /* Mempercantik tampilan scanner bawaan library */
        #reader { border: none !important; }
        #reader__dashboard_section_csr button {
            background-color: #0ea5e9 !important;
            color: white !important;
            padding: 10px 20px !important;
            border-radius: 12px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 10px !important;
            border: none !important;
        }
    </style>
    {{-- Tambahkan ini di atas script scanner --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-layouts.app>