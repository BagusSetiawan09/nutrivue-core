<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pemasok - NutriVue Enterprise</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        primary: '#3b82f6',
                        background: '#050505', // Hitam yang lebih pekat dan elegan
                        surface: '#0a0a0a',
                        borderSubtle: '#1f1f22'
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-background text-[#ededed] antialiased selection:bg-primary/30; }
            
            /* Input Standar Enterprise */
            input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select { 
                @apply w-full bg-[#0d0d0d] border border-borderSubtle rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-[#121212] outline-none transition-all shadow-sm;
            }
            
            /* Label Minimalis */
            label { @apply text-[13px] font-medium text-gray-400 mb-1.5 block; }

            /* Style File Input ala Vercel */
            input[type="file"] {
                @apply block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#1a1a1a] file:text-gray-300 hover:file:bg-[#252525] hover:file:text-white transition-all cursor-pointer;
            }
        }

        /* Animasi Transisi Halus */
        .fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Toggle Switch Ala Apple/Stripe */
        .toggle-checkbox:checked { right: 0; border-color: #3b82f6; }
        .toggle-checkbox:checked + .toggle-label { background-color: #3b82f6; }
    </style>
</head>
<body class="min-h-screen pb-24">

    <nav class="border-b border-borderSubtle bg-background/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-xs font-medium tracking-widest text-gray-400 uppercase">Sistem Registrasi Terbuka</span>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 mt-12 fade-in-up">
        
        <div class="mb-12">
            <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-white mb-3">Registrasi Entitas Pemasok</h1>
            <p class="text-gray-400 text-sm leading-relaxed">Lengkapi detail informasi usaha Anda untuk masuk ke dalam proses verifikasi tim IT MBG NutriVue. Pastikan data yang dimasukkan valid dan sesuai dengan dokumen legal.</p>
        </div>

        <form action="/pendaftaran-pemasok" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf

            <section>
                <div class="border-b border-borderSubtle pb-4 mb-6">
                    <h2 class="text-lg font-medium text-white">1. Profil Operasional</h2>
                    <p class="text-xs text-gray-500 mt-1">Informasi dasar mengenai katering atau unit usaha Anda.</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="nama_usaha">Nama Usaha / Badan Hukum</label>
                        <input type="text" name="nama_usaha" id="nama_usaha" placeholder="PT / CV / Katering..." required>
                    </div>
                    <div>
                        <label for="nama_pemilik">Nama Penanggung Jawab</label>
                        <input type="text" name="nama_pemilik" id="nama_pemilik" placeholder="Sesuai KTP" required>
                    </div>
                    <div>
                        <label for="no_wa">Kontak WhatsApp</label>
                        <input type="tel" name="no_wa" id="no_wa" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email">Alamat Email Korespondensi</label>
                        <input type="email" name="email" id="email" placeholder="kontak@perusahaan.com">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="alamat">Alamat Lengkap Fasilitas / Dapur</label>
                        <textarea name="alamat" id="alamat" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota..." required></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="kapasitas_produksi_harian">Kapasitas Produksi Harian (Estimasi Porsi)</label>
                        <input type="number" name="kapasitas_produksi_harian" id="kapasitas_produksi_harian" placeholder="0" required class="max-w-xs">
                    </div>
                </div>
            </section>

            <section>
                <div class="border-b border-borderSubtle pb-4 mb-6 flex justify-between items-end">
                    <div>
                        <h2 class="text-lg font-medium text-white">2. Ketersediaan Bahan Baku</h2>
                        <p class="text-xs text-gray-500 mt-1">Daftar inventaris harian yang sanggup disuplai secara rutin.</p>
                    </div>
                    <button type="button" onclick="tambahBahanBaku()" class="text-xs font-medium text-primary hover:text-blue-400 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Baris
                    </button>
                </div>
                
                <div id="bahan-baku-container" class="space-y-3">
                    </div>
            </section>

            <section>
                <div class="border-b border-borderSubtle pb-4 mb-6">
                    <h2 class="text-lg font-medium text-white">3. Kepatuhan & Dokumen Legal</h2>
                    <p class="text-xs text-gray-500 mt-1">Verifikasi standar keamanan dan kebersihan fasilitas.</p>
                </div>

                <div class="space-y-6">
                    
                    <div class="flex items-center justify-between p-4 rounded-xl border border-borderSubtle bg-[#0a0a0a]">
                        <div>
                            <p class="text-sm font-medium text-white">Sertifikasi Halal Resmi</p>
                            <p class="text-xs text-gray-500">MUI atau BPJPH</p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="is_halal" id="is_halal" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#1f1f22] appearance-none cursor-pointer z-10 transition-transform duration-300 translate-x-0" onchange="toggleHalal(this)"/>
                            <label for="is_halal" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#1f1f22] cursor-pointer transition-colors duration-300"></label>
                        </div>
                    </div>

                    <div id="halal-input-group" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 border border-borderSubtle rounded-xl bg-[#0a0a0a]">
                        <div>
                            <label for="no_sertifikat_halal">Nomor Registrasi Halal</label>
                            <input type="text" name="no_sertifikat_halal" id="no_sertifikat_halal" placeholder="IDxxxxxxxxxxxx">
                        </div>
                        <div>
                            <label>Dokumen Sertifikat</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="file_sertifikat_halal" accept=".pdf,image/*">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label>Foto Kondisi Dapur / Fasilitas Utama</label>
                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-borderSubtle border-dashed rounded-xl hover:border-primary/50 hover:bg-[#0d0d0d] transition-all group">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-500 group-hover:text-primary transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-400 justify-center">
                                    <input type="file" name="foto_dapur" required class="!w-auto !py-1">
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG maks 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi">Catatan Tambahan / Spesialisasi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi" rows="2" placeholder="Informasi tambahan terkait keunggulan atau kapasitas khusus..."></textarea>
                    </div>
                </div>
            </section>

            <div class="pt-6 border-t border-borderSubtle flex flex-col items-end gap-3">
                <button type="submit" class="w-full sm:w-auto px-8 py-3 text-sm font-semibold text-white bg-white/10 hover:bg-white hover:text-black border border-white/10 rounded-lg transition-all duration-300">
                    Kirim Aplikasi Kemitraan
                </button>
                <p class="text-[11px] text-gray-500 text-right max-w-sm">
                    Dengan menekan tombol di atas, Anda menyatakan bahwa data yang diberikan adalah benar dan siap menerima peninjauan fisik dari otoritas NutriVue.
                </p>
            </div>

        </form>
    </main>

    <script>
        // JS Repeater dengan Animasi Halus
        let count = 0;
        function tambahBahanBaku() {
            const container = document.getElementById('bahan-baku-container');
            const row = document.createElement('div');
            // Desain input inline yang sangat clean
            row.className = 'flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-2';
            row.id = `row-${count}`;
            row.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="bahan_baku_tersedia[${count}][nama_bahan]" placeholder="Nama Bahan (Cth: Ayam Potong)" required class="!border-borderSubtle !bg-transparent">
                </div>
                <div class="w-24">
                    <input type="number" name="bahan_baku_tersedia[${count}][kuantitas]" placeholder="Jml" required class="!border-borderSubtle !bg-transparent text-center">
                </div>
                <div class="w-28">
                    <select name="bahan_baku_tersedia[${count}][satuan]" class="!border-borderSubtle !bg-[#0a0a0a]">
                        <option value="Kg">Kg</option>
                        <option value="Liter">Liter</option>
                        <option value="Ikat">Ikat</option>
                        <option value="Butir">Butir</option>
                    </select>
                </div>
                <button type="button" onclick="hapusRow(${count})" class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Hapus Baris">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            container.appendChild(row);
            
            // Trigger reflow untuk animasi
            void row.offsetWidth; 
            row.classList.remove('opacity-0', 'translate-y-2');
            
            count++;
        }

        function hapusRow(id) {
            const row = document.getElementById(`row-${id}`);
            row.classList.add('opacity-0', '-translate-x-4');
            setTimeout(() => row.remove(), 300);
        }

        // JS Toggle Halal
        function toggleHalal(checkbox) {
            const group = document.getElementById('halal-input-group');
            // Apple style checkbox animation
            if(checkbox.checked) {
                checkbox.style.transform = 'translateX(100%)';
                group.classList.remove('hidden');
                // Trigger animasi masuk
                setTimeout(() => group.classList.add('fade-in-up'), 10);
            } else {
                checkbox.style.transform = 'translateX(0)';
                group.classList.add('hidden');
                group.classList.remove('fade-in-up');
            }
        }

        // Initialize first row
        window.onload = tambahBahanBaku;
    </script>
</body>
</html>