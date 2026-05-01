<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAHIH BODYFEED - Distributor</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Line Awesome -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        html, body {
            font-family: 'Poppins', sans-serif;
            overscroll-behavior: none; /* Prevent bounce/overscroll */
        }
        
        .bg-cream {
            background-color: #FFF7DA;
        }
        
        .bg-orange {
            background-color: #F0A04B;
        }
        
        .bg-green {
            background-color: #84994F;
        }
        
        .text-orange {
            color: #F0A04B;
        }
        
        .text-green {
            color: #84994F;
        }
        
        .border-orange {
            border-color: #F0A04B;
        }
        
        .border-green {
            border-color: #84994F;
        }
        
        .hover\:bg-green-dark:hover {
            background-color: #6d7f42;
        }
        
        .hover\:bg-orange-dark:hover {
            background-color: #d88f3d;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#FFF7DA',
                        orange: '#F0A04B',
                        green: '#84994F',
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased">
    
    
    <!-- Navigation -->
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }" 
         @scroll.window="scrolled = window.pageYOffset > 50"
         :class="scrolled ? 'bg-gray-900/95 backdrop-blur-md shadow-lg' : 'bg-transparent'"
         class="fixed top-0 left-0 right-0 z-50 px-6 lg:px-20 py-4 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('sahihbodyfeed.png') }}" alt="SAHIH BODYFEED" class="h-10 lg:h-14 transition-all duration-300" :class="scrolled ? 'h-8 lg:h-12' : 'h-10 lg:h-14'">
                </a>
            </div>
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-white font-medium hover:text-orange transition">HOME</a>
                <a href="{{ url('/#product') }}" class="text-white font-medium hover:text-orange transition">PRODUCT</a>
                <a href="{{ url('/distributor') }}" class="text-orange font-bold transition">DISTRIBUTOR</a>
            </div>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-white text-3xl z-50 relative">
                <i class="las" :class="mobileMenuOpen ? 'la-times' : 'la-bars'"></i>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             @click.away="mobileMenuOpen = false"
             class="lg:hidden absolute top-full left-0 right-0 bg-green backdrop-blur-sm mt-4 mx-6 rounded-2xl shadow-2xl overflow-hidden"
             style="display: none;">
            <div class="flex flex-col py-4">
                <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="text-white font-medium hover:text-orange transition px-6 py-4">HOME</a>
                <a href="{{ url('/#product') }}" @click="mobileMenuOpen = false" class="text-white font-medium hover:text-orange transition px-6 py-4">PRODUCT</a>
                <a href="{{ url('/distributor') }}" @click="mobileMenuOpen = false" class="text-orange font-bold transition px-6 py-4">DISTRIBUTOR</a>
            </div>
        </div>
    </nav>
    
    <!-- Distributor & Map Section -->
    <section id="distributor" class="relative min-h-screen bg-cover bg-center" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('images/sahihbodyfeed-outdoor.webp') }}');">
        <div class="container mx-auto px-6 lg:px-20 py-32 lg:pt-40 pb-20">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-6xl font-bold text-white mb-8">OUR DISTRIBUTOR SAHIH BODYFEED</h2>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#locator" class="bg-green text-white px-8 py-4 rounded-full font-bold transition">
                        LOKASI DISTRIBUTOR TERDEKAT
                    </a>
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdDcdGOK3p_MIXVG0OUA9Iz1Z4fXvpltL8bEIbhMgUL59c6mw/viewform?usp=header" class="bg-orange text-white px-8 py-4 rounded-full font-bold transition">
                        DAFTAR AGEN
                    </a>
                </div>
            </div>
            
            <!-- Map Panel -->
            <div class="bg-orange rounded-3xl p-12 max-w-4xl mx-auto mt-16">
                <h3 class="text-3xl font-bold text-white text-center mb-6">Distributor Resmi Sahih Bodyfeed</h3>
                <p class="text-center text-white mb-8 max-w-2xl mx-auto">
                    Kami memiliki distributor resmi yang tersebar di seluruh Indonesia untuk memudahkan Anda mendapatkan produk SAHIH BODYFEED yang original dan terpercaya.
                </p>
                <div class="flex justify-center">
                    <img src="{{ asset('images/indonesia-map.svg') }}" alt="Map Distributor Sahih Bodyfeed" class="w-full max-w-2xl">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Locator Section -->
    <section id="locator" class="py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
                <div class="flex justify-center">
                    <img src="{{ asset('images/hero-sahihbodyfeed.avif') }}" alt="SAHIH BODYFEED" class="max-w-md w-full border-8 border-orange rounded-3xl shadow-lg">
                </div>
                
                <div class="bg-orange rounded-3xl p-12">
                    <h3 class="text-3xl font-bold text-white mb-6">Mengapa Harus Beli di Distributor Resmi?</h3>
                    <ul class="space-y-4 text-white">
                        <li class="flex items-start">
                            <i class="las la-check-circle text-2xl mr-3 flex-shrink-0"></i>
                            <span>Produk original dan terjamin kualitasnya</span>
                        </li>
                        <li class="flex items-start">
                            <i class="las la-check-circle text-2xl mr-3 flex-shrink-0"></i>
                            <span>Harga yang sesuai dengan standar pabrik</span>
                        </li>
                        <li class="flex items-start">
                            <i class="las la-check-circle text-2xl mr-3 flex-shrink-0"></i>
                            <span>Garansi kepuasan pelanggan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="las la-check-circle text-2xl mr-3 flex-shrink-0"></i>
                            <span>Layanan konsultasi kesehatan gratis</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Locator Form -->
            <div class="bg-cream rounded-3xl p-12 max-w-4xl mx-auto">
                <h3 class="text-3xl font-bold text-center mb-8">Cari Distributor</h3>
                
                <div class="space-y-6" id="searchForm">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Provinsi</label>
                        <select id="province" class="w-full px-6 py-4 rounded-xl bg-orange/20 border-2 border-orange/30 focus:border-orange focus:outline-none">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Kabupaten/Kota</label>
                        <select id="city" class="w-full px-6 py-4 rounded-xl bg-orange/20 border-2 border-orange/30 focus:border-orange focus:outline-none" disabled>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Kecamatan (Opsional)</label>
                        <select id="district" class="w-full px-6 py-4 rounded-xl bg-orange/20 border-2 border-orange/30 focus:border-orange focus:outline-none" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Kelurahan/Desa (Opsional)</label>
                        <select id="village" class="w-full px-6 py-4 rounded-xl bg-orange/20 border-2 border-orange/30 focus:border-orange focus:outline-none" disabled>
                            <option value="">Pilih Kelurahan/Desa</option>
                        </select>
                    </div>
                    
                    <button id="searchBtn" class="w-full bg-green text-white py-4 rounded-xl font-bold text-lg hover:bg-green-dark transition">
                        CARI DISTRIBUTOR
                    </button>
                </div>

                <!-- Loading State -->
                <div id="loading" class="hidden text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green"></div>
                    <p class="mt-4 text-gray-600">Mencari distributor...</p>
                </div>

                <!-- Results -->
                <div id="results" class="mt-8 hidden">
                    <h4 class="text-2xl font-bold text-center mb-6">Distributor Ditemukan</h4>
                    <div id="distributorList" class="grid md:grid-cols-2 gap-6">
                        <!-- Results will be inserted here -->
                    </div>
                </div>

                <!-- No Results -->
                <div id="noResults" class="hidden text-center py-8">
                    <i class="las la-search text-6xl text-gray-400"></i>
                    <p class="mt-4 text-gray-600 text-lg">Tidak ada distributor ditemukan di wilayah ini.</p>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');
        const villageSelect = document.getElementById('village');
        const searchBtn = document.getElementById('searchBtn');
        const searchForm = document.getElementById('searchForm');
        const loading = document.getElementById('loading');
        const results = document.getElementById('results');
        const noResults = document.getElementById('noResults');
        const distributorList = document.getElementById('distributorList');

        // Load cities when province changes
        provinceSelect.addEventListener('change', async function() {
            const provinceId = this.value;
            citySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            citySelect.disabled = true;
            districtSelect.disabled = true;
            villageSelect.disabled = true;

            if (provinceId) {
                try {
                    const response = await fetch(`/api/provinces/${provinceId}/cities`);
                    const cities = await response.json();
                    
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                    
                    citySelect.disabled = false;
                } catch (error) {
                    console.error('Error loading cities:', error);
                }
            }
        });

        // Load districts when city changes
        citySelect.addEventListener('change', async function() {
            const cityId = this.value;
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            districtSelect.disabled = true;
            villageSelect.disabled = true;

            if (cityId) {
                try {
                    const response = await fetch(`/api/cities/${cityId}/districts`);
                    const districts = await response.json();
                    
                    districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                    
                    districtSelect.disabled = false;
                } catch (error) {
                    console.error('Error loading districts:', error);
                }
            }
        });

        // Load villages when district changes
        districtSelect.addEventListener('change', async function() {
            const districtId = this.value;
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            villageSelect.disabled = true;

            if (districtId) {
                try {
                    const response = await fetch(`/api/districts/${districtId}/villages`);
                    const villages = await response.json();
                    
                    villages.forEach(village => {
                        const option = document.createElement('option');
                        option.value = village.id;
                        option.textContent = village.name;
                        villageSelect.appendChild(option);
                    });
                    
                    villageSelect.disabled = false;
                } catch (error) {
                    console.error('Error loading villages:', error);
                }
            }
        });

        // Search distributors
        searchBtn.addEventListener('click', async function() {
            const provinceId = provinceSelect.value;
            const cityId = citySelect.value;
            const districtId = districtSelect.value;
            const villageId = villageSelect.value;

            if (!provinceId) {
                alert('Silakan pilih provinsi terlebih dahulu');
                return;
            }

            // Show loading
            searchForm.classList.add('hidden');
            loading.classList.remove('hidden');
            results.classList.add('hidden');
            noResults.classList.add('hidden');

            try {
                const params = new URLSearchParams();
                if (provinceId) params.append('province_id', provinceId);
                if (cityId) params.append('city_id', cityId);
                if (districtId) params.append('district_id', districtId);
                if (villageId) params.append('village_id', villageId);

                const response = await fetch(`/api/search-distributor?${params}`);
                const data = await response.json();

                // Hide loading
                loading.classList.add('hidden');
                searchForm.classList.remove('hidden');

                if (data.success && data.data.length > 0) {
                    // Show results
                    distributorList.innerHTML = '';
                    data.data.forEach(distributor => {
                        const card = createDistributorCard(distributor);
                        distributorList.innerHTML += card;
                    });
                    results.classList.remove('hidden');
                } else {
                    // Show no results
                    noResults.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching distributors:', error);
                loading.classList.add('hidden');
                searchForm.classList.remove('hidden');
                alert('Terjadi kesalahan saat mencari distributor');
            }
        });

        function createDistributorCard(distributor) {
            const photoUrl = distributor.photo 
                ? `/storage/${distributor.photo}` 
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(distributor.name)}&size=200&background=F0A04B&color=fff`;
            
            const districts = distributor.districts.join(', ');
            
            return `
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                    <div class="flex items-start gap-4">
                        <img src="${photoUrl}" 
                             alt="${distributor.name}" 
                             class="w-20 h-20 rounded-full object-cover border-4 border-orange">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h5 class="font-bold text-xl text-gray-800">${distributor.name}</h5>
                                <span class="px-3 py-1 bg-green/10 text-green text-xs font-bold rounded-full border border-green/20">
                                    ${distributor.role || 'Distributor'}
                                </span>
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                ${distributor.phone ? `
                                    <div class="flex items-center">
                                        <i class="las la-phone mr-2 text-green"></i>
                                        <a href="https://wa.me/${distributor.phone.replace(/[^0-9]/g, '')}" 
                                           target="_blank" 
                                           class="hover:text-green font-medium">
                                            ${distributor.phone}
                                        </a>
                                    </div>
                                ` : ''}
                                ${distributor.address ? `
                                    <div class="flex items-start">
                                        <i class="las la-map-marker mr-2 text-orange mt-1"></i>
                                        <span>${distributor.address}</span>
                                    </div>
                                ` : ''}
                                ${districts ? `
                                    <div class="flex items-start">
                                        <i class="las la-map mr-2 text-orange mt-1"></i>
                                        <span><strong>Kecamatan:</strong> ${districts}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    </script>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 lg:px-20">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <img src="{{ asset('sahihbodyfeed.png') }}" alt="SAHIH BODYFEED" class="h-12 mb-4">
                    <p class="text-gray-400">Minuman sereal sehat untuk hidup yang lebih baik.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-orange transition">Home</a></li>
                        <li><a href="{{ url('/#product') }}" class="text-gray-400 hover:text-orange transition">Product</a></li>
                        <li><a href="{{ url('/distributor') }}" class="text-gray-400 hover:text-orange transition">Distributor</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center"><i class="las la-phone mr-2"></i> +62 821-2337-3732</li>
                        <li class="flex items-center"><i class="las la-envelope mr-2"></i> halo@sahihbodyfeed</li>
                        <li class="flex items-center"><i class="las la-map-marker mr-2"></i>Jl. Pengayoman, Kompleks Pasar Segar Lantai 1 Blok KBD 11, Kota Makassar, Sulawesi Selatan 90231</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 SAHIH BODYFEED. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-8 right-8 bg-green text-white w-16 h-16 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform z-50">
        <i class="lab la-whatsapp text-4xl"></i>
    </a>
    
</body>
</html>
