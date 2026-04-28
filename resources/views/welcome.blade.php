<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVue Core - Enterprise Nutrition Distribution</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#3b82f6' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    keyframes: {
                        pulseSlow: {
                            '0%, 100%': { opacity: 1, transform: 'scale(1)' },
                            '50%': { opacity: .8, transform: 'scale(0.92)' }
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulseSlow 3s ease-in-out infinite', 
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-[#000000] text-white antialiased; }
        }
        
        /* Animasi Pop-up Sukses */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="antialiased bg-black overflow-x-hidden relative min-h-screen">

    <x-hero />

    @if (session('success'))
    <div id="successModal" class="fixed inset-0 z-[200] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>

        <div class="relative w-full max-w-md p-8 mx-4 overflow-hidden shadow-2xl bg-[#121212] border border-green-500/30 rounded-3xl animate-fade-in-up z-10">
            
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-green-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="text-center relative z-10 pt-4">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-green-500/10 text-green-500 border border-green-500/20 shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h2 class="mb-3 text-2xl font-extrabold text-white tracking-tight">Pendaftaran Berhasil!</h2>
                <p class="mb-8 text-gray-400 text-sm leading-relaxed">
                    {{ session('success') }}
                </p>
                
                <button onclick="document.getElementById('successModal').remove()" class="w-full px-6 py-3.5 text-sm font-bold text-black transition-all rounded-xl bg-green-500 hover:bg-green-400 shadow-[0_0_20px_rgba(34,197,94,0.3)]">
                    Selesai & Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    <div id="supplierModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden opacity-0 transition-opacity duration-500">
        <div id="modalOverlay" class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>

        <div id="modalContent" class="relative w-full max-w-lg p-8 mx-4 overflow-hidden shadow-2xl bg-[#121212] border border-white/10 rounded-3xl transform scale-95 transition-transform duration-500">
            
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

            <button id="closeModalBtn" class="absolute top-5 right-5 z-[110] p-2 text-gray-400 bg-white/5 rounded-full hover:text-white hover:bg-white/10 transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center relative z-10 pt-4">
                <div class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-2xl bg-primary/10 text-primary border border-primary/20">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 007.5 9.75c.627 0 1.227-.151 1.754-.423.527.272 1.127.423 1.746.423.618 0 1.218-.151 1.754-.423.527.272 1.127.423 1.746.423a3.75 3.75 0 004.901-5.652l-1.3-1.298a1.875 1.875 0 00-1.325-.55H5.223z" />
                        <path d="M3 12v7.5A2.25 2.25 0 005.25 21.75h13.5A2.25 2.25 0 0021 19.5V12c-.179.028-.363.043-.55.043a3.729 3.729 0 01-1.746-.423 3.73 3.73 0 01-1.754.423 3.729 3.729 0 01-1.746-.423 3.73 3.73 0 01-1.754.423 3.729 3.729 0 01-1.746-.423c-.187 0-.371-.015-.55-.043zM13.5 20.25h-3v-2.25a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v2.25z" />
                    </svg>
                </div>
                
                <h2 class="mb-3 text-2xl font-extrabold text-white sm:text-3xl tracking-tight">Punya Kapasitas Pangan?</h2>
                <p class="mb-8 text-gray-400 text-sm sm:text-base leading-relaxed">
                    Dukung program gizi nasional dengan menyuplai bahan pangan berkualitas. Transparan, terstruktur, dan memberdayakan ekonomi lokal.
                </p>
                
                <a href="/pendaftaran-pemasok" class="inline-flex items-center justify-center w-full px-6 py-4 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-blue-600 shadow-lg">
                    Daftar Sebagai Pemasok Sekarang
                </a>
            </div>
        </div>
    </div>

    <div id="floatingBubble" class="fixed z-[90] bottom-8 left-8 hidden transform translate-y-10 opacity-0 transition-all duration-700 ease-out flex items-center gap-3">
        <a href="/pendaftaran-pemasok" class="relative flex items-center justify-center w-14 h-14 text-white shadow-xl bg-primary border border-white/20 rounded-full transition-transform hover:scale-110 z-10">
            <svg class="w-7 h-7 text-white/90 animate-pulse-slow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
              <path fill-rule="evenodd" d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z" clip-rule="evenodd" />
            </svg>
            
            <span class="absolute -top-1 -right-1 flex w-4 h-4">
              <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-blue-300"></span>
              <span class="relative inline-flex w-4 h-4 rounded-full bg-white shadow-sm"></span>
            </span>
        </a>

        <div id="bubbleMessage" class="px-5 py-2.5 text-sm font-semibold text-white shadow-2xl bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl opacity-0 transition-opacity duration-500 whitespace-nowrap pointer-events-none">
            Jadi Pemasok NutriVue 👋
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('supplierModal');
            const modalContent = document.getElementById('modalContent');
            const modalOverlay = document.getElementById('modalOverlay');
            const closeBtn = document.getElementById('closeModalBtn');
            
            const floatingBubble = document.getElementById('floatingBubble');
            const bubbleMessage = document.getElementById('bubbleMessage');

            // ⚡ CEK APAKAH USER BARU SAJA BERHASIL MENDAFTAR
            const hasSuccessModal = {{ session('success') ? 'true' : 'false' }};

            if (hasSuccessModal) {
                // Jika sukses, matikan pop-up promo pendaftaran selamanya di sesi ini
                sessionStorage.setItem('supplierModalClosed', 'true');
                // Tetap munculkan bubble kecil di pojok
                showFloatingBubble();
            } else {
                // LOGIKA PROMO NORMAL (JIKA BELUM DAFTAR)
                const isModalClosed = sessionStorage.getItem('supplierModalClosed');

                if (!isModalClosed) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modalContent.classList.remove('scale-95');
                        modalContent.classList.add('scale-100');
                    }, 100);
                } else {
                    showFloatingBubble();
                }
            }

            // Fungsi Penutup Modal Promo
            const hideModal = () => {
                modal.classList.add('opacity-0');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    sessionStorage.setItem('supplierModalClosed', 'true');
                    showFloatingBubble();
                }, 500);
            };

            closeBtn.addEventListener('click', hideModal);
            modalOverlay.addEventListener('click', hideModal);

            function showFloatingBubble() {
                floatingBubble.classList.remove('hidden');
                setTimeout(() => {
                    floatingBubble.classList.remove('translate-y-10', 'opacity-0');
                    startBubbleAnimation();
                }, 200);
            }

            function startBubbleAnimation() {
                setTimeout(triggerMessage, 2000); 
                setInterval(triggerMessage, 10000); 
            }

            function triggerMessage() {
                bubbleMessage.classList.remove('opacity-0');
                setTimeout(() => {
                    bubbleMessage.classList.add('opacity-0');
                }, 4000); 
            }
        });
    </script>
</body>
</html>