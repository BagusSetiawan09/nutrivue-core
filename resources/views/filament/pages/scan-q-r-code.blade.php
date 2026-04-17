<x-filament-panels::page>
    <style>
        #reader { width: 100% !important; border: none !important; background: transparent !important; }
        #reader video { width: 100% !important; border-radius: 0.75rem !important; }
        #reader__dashboard_section_csr span, #reader__dashboard_section_swaplink { display: none !important; }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <div class="flex flex-col gap-6">
            <x-filament::section>
                <x-slot name="heading">Optik Scanner</x-slot>
                <x-slot name="description">Arahkan QR Code ke kamera untuk verifikasi.</x-slot>

                <div wire:ignore class="relative w-full rounded-xl overflow-hidden bg-black border border-gray-200 dark:border-gray-700 flex items-center justify-center shadow-inner">
                    <div id="reader" class="w-full"></div>
                </div>

                <div class="mt-6 p-4 bg-warning-50 dark:bg-warning-500/10 rounded-xl border border-warning-200 dark:border-warning-500/30">
                    <p class="text-xs font-bold text-warning-700 dark:text-warning-400 mb-2 uppercase tracking-widest flex items-center gap-1">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" /> Input Manual
                    </p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-qr-input" placeholder="Paste Kode QR..." class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder-gray-400">
                        <x-filament::button onclick="manualSubmit()" color="warning">Tembak!</x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <div class="flex flex-col gap-6">
            <x-filament::section>
                <x-slot name="heading">Data Verifikasi</x-slot>
                <x-slot name="description">Hasil pindaian akan muncul di sini secara real-time.</x-slot>

                <div class="flex flex-col justify-center min-h-[400px]">
                    
                    @if($scanStatus === 'idle')
                        <div class="flex flex-col items-center text-center text-gray-400 dark:text-gray-500">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                <x-heroicon-o-qr-code class="w-10 h-10" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 dark:text-gray-300">Sistem Standby</h3>
                            <p class="text-sm mt-1">Kamera aktif. Menunggu pindaian...</p>
                        </div>

                    @elseif($scanStatus === 'success')
                        <div class="animate-pulse-once">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-success-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-success-500/30 shrink-0">
                                    <x-heroicon-s-check class="w-8 h-8" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $scannedData['name'] ?? 'Data Tidak Valid' }}</h2>
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-bold bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-400 uppercase tracking-wider mt-1">
                                        {{ $scannedData['kategori'] ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 space-y-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1 font-semibold">Status Validasi</p>
                                        <div class="flex items-center gap-1.5 text-success-600 dark:text-success-400 font-bold">
                                            <div class="w-2 h-2 rounded-full bg-success-500 animate-pulse"></div>
                                            SAH / VALID
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1 font-semibold">Waktu Scan</p>
                                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ now()->format('H:i:s WIB') }}</p>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1 font-semibold">Instruksi Sistem</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-relaxed">{{ $scanMessage }}</p>
                                </div>
                            </div>
                        </div>

                    @elseif($scanStatus === 'error')
                        <div class="animate-pulse-once">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-danger-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-danger-500/30 shrink-0">
                                    <x-heroicon-s-x-mark class="w-8 h-8" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-danger-600 dark:text-danger-500 tracking-tight">Akses Ditolak</h2>
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-bold bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400 uppercase tracking-wider mt-1">
                                        Sistem Anti-Fraud
                                    </span>
                                </div>
                            </div>

                            <div class="bg-danger-50 dark:bg-danger-500/10 rounded-2xl border border-danger-200 dark:border-danger-500/20 p-5 space-y-4">
                                <div>
                                    <p class="text-[10px] text-danger-600 dark:text-danger-400 uppercase tracking-widest mb-1 font-semibold">Alasan Penolakan</p>
                                    <p class="font-bold text-danger-800 dark:text-danger-300">{{ $scanMessage }}</p>
                                </div>
                                
                                @if($scannedData)
                                <div class="pt-4 border-t border-danger-200 dark:border-danger-500/20 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] text-danger-600 dark:text-danger-400 uppercase tracking-widest mb-1 font-semibold">Data Terdeteksi</p>
                                        <p class="font-bold text-danger-900 dark:text-danger-100">{{ $scannedData['name'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-danger-600 dark:text-danger-400 uppercase tracking-widest mb-1 font-semibold">Kategori</p>
                                        <p class="font-bold text-danger-900 dark:text-danger-100 uppercase">{{ $scannedData['kategori'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </x-filament::section>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <script>
        function manualSubmit() {
            let inputVal = document.getElementById('manual-qr-input').value;
            if(inputVal) { @this.verifyScannedQr(inputVal); }
        }

        document.addEventListener('livewire:initialized', () => {
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = false;
            
            const onScanSuccess = (decodedText) => {
                if (isScanning) return;
                isScanning = true;
                html5QrCode.pause();
                @this.verifyScannedQr(decodedText); 
            };

            setTimeout(() => {
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    { fps: 10, qrbox: 250, aspectRatio: 1.0 }, 
                    onScanSuccess
                ).catch(err => console.error("Kamera Error: ", err));
            }, 500);

            window.addEventListener('scan-finished', () => {
                setTimeout(() => {
                    isScanning = false;
                    document.getElementById('manual-qr-input').value = ''; 
                    if (html5QrCode.getState() === 2) { 
                        html5QrCode.resume();
                    }
                }, 3000); 
            });
        });
    </script>
</x-filament-panels::page>