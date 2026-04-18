<section id="hero-section" class="relative bg-black text-white min-h-screen flex items-center justify-center overflow-hidden font-sans selection:bg-white/30 selection:text-white group">
    
    <style>
        /* Animasi Wave Text */
        @keyframes letterWave {
            0% { opacity: 0; transform: translateY(25px) rotate(3deg); }
            100% { opacity: 1; transform: translateY(0) rotate(0deg); }
        }
        .animate-letter {
            display: inline-block;
            opacity: 0;
            animation: letterWave 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Animasi Fade Up */
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Animasi Infinite Scroll */
        @keyframes infiniteScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-50% - 0.5rem)); } 
        }
        .animate-infinite-scroll {
            animation: infiniteScroll 15s linear infinite;
        }
        
        .animate-infinite-scroll:hover {
            animation-play-state: paused;
        }
    </style>

    <div class="absolute inset-0 opacity-[0.03] mix-blend-screen pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_80%_at_50%_0%,#000_70%,transparent_100%)] pointer-events-none"></div>

    <div class="pointer-events-none absolute -inset-px transition duration-300 opacity-0 group-hover:opacity-100 z-0" 
         style="background: radial-gradient(800px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255,255,255,0.06), transparent 40%);">
    </div>

    <div class="container mx-auto px-6 relative z-10 py-24 lg:py-0 max-w-7xl pointer-events-auto">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-12">

            <div class="lg:w-1/2 flex flex-col items-center text-center lg:items-start lg:text-left">
                
                <div class="animate-fade-up inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/[0.03] text-xs font-medium text-zinc-400 mb-8 backdrop-blur-md transition-colors hover:bg-white/[0.05]" style="animation-delay: 0.1s;">
                    <svg class="w-3 h-3 text-zinc-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>NutriVue Enterprise Edition</span>
                </div>
                
                <h1 class="text-6xl sm:text-7xl lg:text-[5.5rem] font-medium tracking-tighter leading-[1.05] mb-6">
                    <span class="wave-text block text-white drop-shadow-md">Infrastruktur</span>
                    <span class="wave-text block text-zinc-400" data-delay="0.3">Gizi Cerdas.</span>
                </h1>
                                
                <p class="animate-fade-up text-lg text-zinc-500 max-w-md leading-relaxed font-light mb-10" style="animation-delay: 1s;">
                    Platform pengelola data berkinerja tinggi. Pantau, verifikasi, dan distribusikan nutrisi dalam skala kota tanpa latensi.
                </p>

                <div class="animate-fade-up flex flex-col sm:flex-row gap-4 w-full sm:w-auto" style="animation-delay: 1.2s;">
                    <a href="{{ asset('download/nutrivue-app.apk') }}" download="NutriVue-Enterprise-v1.apk" class="inline-flex justify-center items-center gap-2 h-12 px-8 bg-white text-black rounded-full text-sm font-semibold hover:bg-zinc-200 transition-all active:scale-95 shadow-[0_0_40px_rgba(255,255,255,0.1)] relative overflow-hidden group/btn">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
                        <span class="relative">Unduh Aplikasi</span>
                    </a>
                    <a href="/admin/login" class="inline-flex justify-center items-center gap-2 h-12 px-8 bg-transparent border border-white/10 text-white rounded-full text-sm font-medium hover:bg-white/[0.05] transition-all active:scale-95">
                        Akses Dashboard
                    </a>
                </div>
            </div>

            <div class="lg:w-1/2 w-full relative flex justify-center lg:justify-end mt-12 lg:mt-0">
                <div class="relative w-full max-w-[540px]">
                    <div class="grid grid-cols-2 gap-4">
                        
                        <div class="animate-fade-up col-span-2 rounded-3xl border border-white/10 bg-gradient-to-b from-white/[0.05] to-transparent p-6 flex flex-col justify-between backdrop-blur-2xl hover:border-white/30 transition-colors duration-500 overflow-hidden relative group/card" style="animation-delay: 1.4s;">
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-zinc-400 mb-1">Total Distribusi</p>
                                    <h3 class="text-4xl font-semibold tracking-tighter text-white">24,592<span class="text-xl text-zinc-600">/porsi</span></h3>
                                </div>
                                <div class="px-2.5 py-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-medium flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Live
                                </div>
                            </div>
                            <div class="relative z-10 h-16 w-full mt-4 flex items-end gap-1 opacity-70 group-hover/card:opacity-100 transition-opacity">
                                <div class="w-full bg-white/10 hover:bg-white/40 transition-colors rounded-t-sm h-[30%]"></div>
                                <div class="w-full bg-white/10 hover:bg-white/40 transition-colors rounded-t-sm h-[50%]"></div>
                                <div class="w-full bg-white/20 hover:bg-white/40 transition-colors rounded-t-sm h-[40%]"></div>
                                <div class="w-full bg-white/20 hover:bg-white/40 transition-colors rounded-t-sm h-[70%]"></div>
                                <div class="w-full bg-white/30 hover:bg-white/60 transition-colors rounded-t-sm h-[60%]"></div>
                                <div class="w-full bg-white hover:shadow-[0_0_15px_rgba(255,255,255,0.5)] transition-all rounded-t-sm h-[100%]"></div>
                            </div>
                        </div>

                        <div class="animate-fade-up col-span-1 rounded-3xl border border-white/10 bg-white/[0.02] p-5 flex flex-col items-center justify-center text-center backdrop-blur-2xl hover:bg-white/[0.06] hover:border-white/20 transition-all duration-300" style="animation-delay: 1.6s;">
                            <div class="w-12 h-12 rounded-2xl border border-white/10 bg-white/5 flex items-center justify-center mb-3 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <h4 class="text-zinc-200 font-medium text-sm">QR Engine</h4>
                        </div>

                        <div class="animate-fade-up col-span-1 rounded-3xl border border-white/10 bg-white/[0.02] p-5 flex flex-col items-center justify-center text-center backdrop-blur-2xl hover:bg-white/[0.06] hover:border-white/20 transition-all duration-300" style="animation-delay: 1.8s;">
                            <div class="relative w-12 h-12 mb-3 group/circle">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="2" fill="transparent" class="text-white/10" />
                                    <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="2" fill="transparent" stroke-dasharray="138" stroke-dashoffset="10" class="text-white transition-all duration-700 group-hover/circle:stroke-dashoffset-0" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-white">99%</span>
                                </div>
                            </div>
                            <h4 class="text-zinc-200 font-medium text-sm">Akurasi Server</h4>
                        </div>

                        <div class="animate-fade-up col-span-2 rounded-3xl border border-white/10 bg-gradient-to-tr from-blue-900/10 to-transparent bg-[#0a0a0c] py-6 backdrop-blur-2xl hover:border-white/20 transition-all duration-300 relative overflow-hidden" style="animation-delay: 2.0s;">
                            
                            <div class="flex justify-between items-center mb-4 px-6 relative z-10">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.8)] animate-pulse"></div>
                                    <span class="text-xs font-mono tracking-widest text-zinc-400 uppercase">Jury Access Node</span>
                                </div>
                                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>

                            <div class="relative w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
                                
                                <div class="flex gap-4 w-max animate-infinite-scroll">
                                    
                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Super Admin</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">superadmin@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>
                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> Pemerintah</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">pemerintah@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>
                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Petugas</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">petugas@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>

                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0" aria-hidden="true">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Super Admin</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">superadmin@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>
                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0" aria-hidden="true">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> Pemerintah</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">pemerintah@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>
                                    <div class="w-56 p-3 rounded-xl border border-white/5 bg-black/40 hover:bg-black/80 transition-colors shrink-0" aria-hidden="true">
                                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider mb-1 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Petugas</p>
                                        <p class="text-[11px] text-white font-mono mb-0.5">petugas@nutrivueapp.com</p>
                                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                            <span class="text-[10px] text-zinc-500">Pass:</span>
                                            <span class="text-[11px] text-zinc-300 font-mono">password123</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Efek Mouse Spotlight
        const heroSection = document.getElementById('hero-section');
        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            heroSection.style.setProperty('--mouse-x', `${x}px`);
            heroSection.style.setProperty('--mouse-y', `${y}px`);
        });

        // Efek Wave Text
        const waveElements = document.querySelectorAll('.wave-text');
        waveElements.forEach((el) => {
            const text = el.innerText.trim();
            const baseDelay = parseFloat(el.getAttribute('data-delay') || '0');
            el.innerHTML = '';
            
            text.split('').forEach((char, index) => {
                const span = document.createElement('span');
                span.innerHTML = char === ' ' ? '&nbsp;' : char;
                span.className = 'animate-letter';
                span.style.animationDelay = `${baseDelay + (index * 0.04)}s`;
                el.appendChild(span);
            });
        });
    });
</script>