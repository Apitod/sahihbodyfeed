<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahih Bodyfeed - Minuman Sereal Sehat</title>
    
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
    
    <!-- Hero Section -->
    <section class="relative min-h-screen bg-cover bg-center" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url({{ asset('images/hero-sahihbodyfeed.avif') }});">
        <!-- Navigation -->
        <nav x-data="{ mobileMenuOpen: false, scrolled: false }" 
             @scroll.window="scrolled = window.pageYOffset > 50"
             :class="scrolled ? 'bg-gray-900/95 backdrop-blur-md shadow-lg' : 'bg-transparent'"
             class="fixed top-0 left-0 right-0 z-50 px-6 lg:px-20 py-4 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="{{ asset('sahihbodyfeed.png') }}" alt="SAHIH BODYFEED" class="h-40 lg:h-50 transition-all duration-300" :class="scrolled ? 'h-10 lg:h-14' : 'h-10 lg:h-14'">
                </div>
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-white font-medium hover:text-orange transition">HOME</a>
                    <a href="#product" class="text-white font-medium hover:text-orange transition">PRODUCT</a>
                    <a href="{{ url('/distributor') }}" class="text-white font-medium hover:text-orange transition">DISTRIBUTOR</a>
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
                    <a href="#home" @click="mobileMenuOpen = false" class="text-white font-medium hover:text-orange transition px-6 py-4">HOME</a>
                    <a href="#product" @click="mobileMenuOpen = false" class="text-white font-medium hover:text-orange transition px-6 py-4">PRODUCT</a>
                    <a href="{{ url('/distributor') }}" @click="mobileMenuOpen = false" class="text-white font-medium hover:text-orange transition px-6 py-4">DISTRIBUTOR</a>
                </div>
            </div>
        </nav>
        
        <!-- Hero Content -->
        <div class="container mx-auto px-6 lg:px-20 pt-32 lg:pt-40 pb-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white text-center lg:text-left">
                    <h1 class="text-3xl md:text-4xl lg:text-6xl font-bold leading-tight mb-6">
                        IT'S TIME TO LIVE <span class="text-orange">HEALTHIER</span> WITH SAHIH BODYFEED
                    </h1>
                    <p class="text-lg md:text-xl lg:text-2xl mb-6">Minuman Sereal dengan biji bijian dan Labu Kuning</p>
                    <div class="flex items-center justify-center lg:justify-start mb-8">
                        <i class="las la-check-circle text-green text-2xl mr-2"></i>
                        <span class="text-base md:text-lg">Halal MUI & Terdaftar BPOM</span>
                    </div>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/distributor') }}" class="bg-green text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-dark transition inline-flex items-center justify-center">
                            BELI SAHIH <i class="las la-angle-right ml-2"></i>
                        </a>
                        <a href="#" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-gray-800 transition">
                            ABOUT SAHIH
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Introduction & Benefits Section -->
    <section id="product" class="bg-cream py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <h2 class="text-4xl lg:text-5xl font-bold text-center mb-6">
                Kenapa <span class="text-orange">SAHIH BODYFEED</span>?
            </h2>
            <div class="max-w-4xl mx-auto text-center mb-16">
                <p class="text-lg text-gray-700 leading-relaxed mb-4">
                    Tubuh butuh asupan yang benar, bukan sekadar kenyang. Sahih Bodyfeed hadir sebagai minuman multigrain yang menggabungkan kacang-kacangan, biji-bijian, dan labu kuning untuk mendukung pola hidup sehat setiap hari.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Diproduksi dengan standar yang terjaga, Sahih Bodyfeed telah bersertifikat Halal dan terdaftar di BPOM, sehingga aman dikonsumsi sebagai bagian dari rutinitas harian.
                </p>
            </div>
            
            <!-- Product with Benefits - Responsive Layout -->
            <!-- Product with Benefits - Responsive Layout -->
            <!-- Desktop: Radial Layout with Image -->
            <div class="hidden lg:block relative max-w-6xl mx-auto min-h-[600px]">
                <!-- SVG Defs for Arrows -->
                <svg class="absolute w-0 h-0">
                    <defs>
                        <marker id="arrowhead-orange" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#F0A04B" />
                        </marker>
                    </defs>
                </svg>

                <div class="flex items-center justify-center h-full pt-10">
                    <img src="{{ asset('images/coba.png') }}" alt="SAHIH BODYFEED" class="w-full max-w-lg z-10 relative">
                </div>
                
                <!-- Left Side Items -->
                <div class="absolute top-24 left-0 lg:left-10 flex items-center justify-end w-80">
                    <div class="text-right mr-4">
                        <h3 class="font-bold text-lg leading-tight">Menstabilkan<br>kadar gula tinggi</h3>
                    </div>
                    <svg class="w-24 h-16 text-orange fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 100 60 Q 50 60 0 10" marker-end="url(#arrowhead-orange)" />
                    </svg>
                </div>
                
                <div class="absolute bottom-32 left-0 lg:left-10 flex items-center justify-end w-80">
                    <div class="text-right mr-4">
                        <h3 class="font-bold text-lg leading-tight">Mengatasi Masalah<br>Pencernaan (GERD,<br>Maag, dll)</h3>
                    </div>
                    <svg class="w-24 h-16 text-orange fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 100 0 Q 50 0 0 50" marker-end="url(#arrowhead-orange)" />
                    </svg>
                </div>
                
                <!-- Right Side Items -->
                <div class="absolute top-24 right-0 lg:right-10 flex items-center w-80">
                    <svg class="w-24 h-16 text-orange fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 0 60 Q 50 60 100 10" marker-end="url(#arrowhead-orange)" />
                    </svg>
                   <div class="text-left ml-4">
                        <h3 class="font-bold text-lg leading-tight">Menurunkan<br>Resiko Penyakit<br>Jantung</h3>
                    </div>
                </div>

                <div class="absolute top-1/2 right-0 lg:-right-8 transform -translate-y-1/2 flex items-center w-80">
                    <svg class="w-24 h-8 text-orange fill-none stroke-current stroke-2" viewBox="0 0 100 30">
                         <path d="M 0 15 Q 50 15 100 15" marker-end="url(#arrowhead-orange)" />
                    </svg>
                    <div class="text-left ml-4">
                        <h3 class="font-bold text-lg leading-tight">Menurunkan<br>Kadar Kolestrol</h3>
                    </div>
                </div>
                
                <div class="absolute bottom-32 right-0 lg:right-10 flex items-center w-80">
                     <svg class="w-24 h-16 text-orange fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 0 0 Q 50 0 100 50" marker-end="url(#arrowhead-orange)" />
                    </svg>
                    <div class="text-left ml-4">
                        <h3 class="font-bold text-lg leading-tight">Merangsang<br>Pertumbuhan<br>Bakteri Baik di usus</h3>
                    </div>
                </div>
            </div>
            
            <!-- Mobile: Stacked Grid Layout -->
            <div class="lg:hidden">
                <!-- Product Image -->
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/coba.png') }}" alt="SAHIH BODYFEED" class="w-64">
                </div>
                
                <!-- Benefits Grid -->
                <div class="grid gap-4 px-4">
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-start">
                            <i class="las la-heartbeat text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-base">Mengatasi Masalah Pencernaan</h3>
                                <p class="text-gray-600 text-sm">(GERD, Maag, dll)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-start">
                            <i class="las la-chart-line text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-base">Menurunkan Kadar Kolestrol</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-start">
                            <i class="las la-heart text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-base">Menurunkan Resiko Penyakit Jantung</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-start">
                            <i class="las la-bacterium text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-base">Merangsang Pertumbuhan Bakteri Baik</h3>
                                <p class="text-gray-600 text-sm">di usus</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-start">
                            <i class="las la-tint text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-base">Menstabilkan Kadar Gula Tinggi</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Product Composition Section -->
    <section class="bg-orange py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-bold text-white mb-4">Komposisi Produk</h2>
                <p class="text-white text-lg">Diproses dengan konsep minuman sereal modern yang mudah dikonsumsi kapan saja</p>
            </div>
            
            <!-- Desktop: Radial Layout with Image -->
            <div class="hidden lg:block relative max-w-6xl mx-auto min-h-[600px]">
                <!-- SVG Defs for Arrows -->
                <svg class="absolute w-0 h-0">
                    <defs>
                        <marker id="arrowhead-cream" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#FFF7DA" />
                        </marker>
                    </defs>
                </svg>

                <div class="flex items-center justify-center h-full pt-10">
                    <img src="{{ asset('images/product-compositions.jpg') }}" alt="Product Composition" class="w-full max-w-lg z-10 relative">
                </div>
                
                <!-- Left Side Ingredients -->
                <div class="absolute top-24 left-0 lg:left-10 flex items-center justify-end w-80">
                    <div class="text-right mr-4">
                        <h3 class="font-bold text-xl text-white leading-tight">Gandum &<br>Labu Kuning</h3>
                    </div>
                    <svg class="w-24 h-16 text-cream fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 100 60 Q 50 60 0 10" marker-end="url(#arrowhead-cream)" />
                    </svg>
                </div>
                
                <div class="absolute bottom-32 left-0 lg:left-10 flex items-center justify-end w-80">
                    <div class="text-right mr-4">
                        <h3 class="font-bold text-xl text-white leading-tight">Kacang -<br>Kacangan<br>Bernutrisi</h3>
                    </div>
                     <svg class="w-24 h-16 text-cream fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 100 0 Q 50 0 0 50" marker-end="url(#arrowhead-cream)" />
                    </svg>
                </div>
                
                <!-- Right Side Ingredients -->
                <div class="absolute top-24 right-0 lg:right-10 flex items-center w-80">
                     <svg class="w-24 h-16 text-cream fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 0 60 Q 50 60 100 10" marker-end="url(#arrowhead-cream)" />
                    </svg>
                    <div class="text-left ml-4">
                        <h3 class="font-bold text-xl text-white leading-tight">Malt & Oat</h3>
                    </div>
                </div>
                
                 <div class="absolute top-1/2 right-0 lg:-right-5 transform -translate-y-1/2 flex items-center w-80">
                    <svg class="w-24 h-16 text-cream fill-none stroke-current stroke-2" viewBox="0 0 100 30">
                         <path d="M 0 15 Q 50 15 100 15" marker-end="url(#arrowhead-cream)" />
                    </svg>
                    <div class="text-left ml-4">
                        <h3 class="font-bold text-xl text-white leading-tight">Psyllium<br>Husk</h3>
                    </div>
                </div>

                <div class="absolute bottom-32 right-0 lg:right-10 flex items-center w-80">
                    <svg class="w-24 h-16 text-cream fill-none stroke-current stroke-2" viewBox="0 0 100 60">
                         <path d="M 0 0 Q 50 0 100 50" marker-end="url(#arrowhead-cream)" />
                    </svg>
                    <div class="text-left ml-4">
                        <h3 class="font-bold text-xl text-white leading-tight">Biji - Bijian<br>Kaya Serat</h3>
                    </div>
                </div>
            </div>
            
            <!-- Mobile: Stacked Grid Layout -->
            <div class="lg:hidden">
                <!-- Product Image -->
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/product-compositions.jpg') }}" alt="Product Composition" class="w-64">
                </div>
                
                <!-- Ingredients Grid -->
                <div class="grid gap-4 px-4">
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-center">
                            <i class="las la-seedling text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <h3 class="font-bold text-base">Malt & Oat</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-center">
                            <i class="las la-leaf text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <h3 class="font-bold text-base">Gandum & Labu Kuning</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-center">
                            <i class="las la-apple-alt text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <h3 class="font-bold text-base">Biji-Bijian Kaya Serat</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-center">
                            <i class="las la-spa text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <h3 class="font-bold text-base">Psyllium Husk</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-4 shadow">
                        <div class="flex items-center">
                            <i class="las la-mortar-pestle text-orange text-3xl mr-3 flex-shrink-0"></i>
                            <h3 class="font-bold text-base">Kacang-Kacangan Bernutrisi</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Serving Suggestion Section -->
    <section class="bg-cream py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <h2 class="text-4xl lg:text-5xl font-bold text-center mb-16">Penyajian SAHIH BODYFEED</h2>
            
            <div class="grid lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
                <div class="flex justify-center">
                    <img src="{{ asset('images/penyajian-produk.avif') }}" alt="Serving Suggestion" class="max-w-md w-full rounded-2xl shadow-xl">
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-start bg-white rounded-xl p-6 shadow">
                        <div class="bg-orange text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-xl mr-4 flex-shrink-0">1</div>
                        <p class="text-lg pt-2">Tuang air panas/hangat 150 ml ke dalam gelas</p>
                    </div>
                    
                    <div class="flex items-start bg-white rounded-xl p-6 shadow">
                        <div class="bg-orange text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-xl mr-4 flex-shrink-0">2</div>
                        <p class="text-lg pt-2">Masukkan 3 sendok makan atau setara 25 gram SAHIH BODYFEED</p>
                    </div>
                    
                    <div class="flex items-start bg-white rounded-xl p-6 shadow">
                        <div class="bg-orange text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-xl mr-4 flex-shrink-0">3</div>
                        <p class="text-lg pt-2">Aduk hingga tercampur rata</p>
                    </div>
                    
                    <div class="flex items-start bg-white rounded-xl p-6 shadow">
                        <div class="bg-orange text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-xl mr-4 flex-shrink-0">4</div>
                        <p class="text-lg pt-2">SAHIH BODYFEED siap disajikan!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="bg-orange py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <!-- Testimonial Header -->
            <div class="grid lg:grid-cols-2 gap-8 items-center mb-16">
                <div class="text-center lg:text-left">
                    <h2 class="text-3xl lg:text-5xl font-bold text-white leading-tight">
                        Ini yang mereka katakan tentang produk kami
                    </h2>
                </div>
                
                <!-- Carousel -->
                <div x-data="{ activeSlide: 0, slides: 3 }" class="relative min-w-0">
                    <div class="overflow-hidden rounded-2xl">
                        <div class="flex w-full transition-transform duration-500" :style="`transform: translateX(-${activeSlide * 100}%)`">
                            <!-- Slide 1 -->
                            <div class="w-full flex-shrink-0 bg-white p-8 rounded-2xl">
                                <div class="flex items-center mb-4">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Putri&background=F0A04B&color=fff" alt="Customer" class="w-16 h-16 rounded-full mr-4">
                                    <div>
                                        <h4 class="font-bold text-lg">@sarah_putri</h4>
                                        <div class="flex text-orange">
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-700">"Alhamdulillah, sejak rutin minum SAHIH BODYFEED, pencernaan saya jadi lebih lancar dan maag saya berkurang!"</p>
                            </div>
                            
                            <!-- Slide 2 -->
                            <div class="w-full flex-shrink-0 bg-white p-8 rounded-2xl">
                                <div class="flex items-center mb-4">
                                    <img src="https://ui-avatars.com/api/?name=Bapak+Acha&background=F0A04B&color=fff" alt="Customer" class="w-16 h-16 rounded-full mr-4">
                                    <div>
                                        <h4 class="font-bold text-lg">@bapak_acha</h4>
                                        <div class="flex text-orange">
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-700">"Produk yang sangat membantu untuk menjaga kesehatan. Rasanya enak dan mudah disajikan!"</p>
                            </div>
                            
                            <!-- Slide 3 -->
                            <div class="w-full flex-shrink-0 bg-white p-8 rounded-2xl">
                                <div class="flex items-center mb-4">
                                    <img src="https://ui-avatars.com/api/?name=Thanoe&background=F0A04B&color=fff" alt="Customer" class="w-16 h-16 rounded-full mr-4">
                                    <div>
                                        <h4 class="font-bold text-lg">@thanoe</h4>
                                        <div class="flex text-orange">
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                            <i class="las la-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-700">"Recommended banget! Kolesterol saya turun setelah rutin konsumsi SAHIH BODYFEED."</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="flex justify-center mt-6 space-x-2">
                        <template x-for="i in slides" :key="i">
                            <button @click="activeSlide = i - 1" :class="activeSlide === i - 1 ? 'bg-white' : 'bg-white/50'" class="w-3 h-3 rounded-full transition"></button>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- CTA -->
            <div class="text-center">
                <h3 class="text-3xl font-bold text-white mb-6">Beli di Distributor Resmi</h3>
                <a href="{{ url('/distributor') }}" class="bg-green text-white px-12 py-4 rounded-full font-bold text-lg hover:bg-green-dark transition inline-block">
                    BELI SEKARANG
                </a>
            </div>
        </div>
    </section>
    

    
    <!-- FAQ Section -->
    <section class="bg-cream py-20">
        <div class="container mx-auto px-6 lg:px-20">
            <h2 class="text-4xl lg:text-5xl font-bold text-center mb-16">Frequently Asked Questions</h2>
            
            <div x-data="{ openFaq: null }" class="max-w-4xl mx-auto space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Apa itu Sahih Bodyfeed?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Sahih Bodyfeed adalah minuman sereal multigrain yang terbuat dari kombinasi kacang-kacangan, biji-bijian, dan labu kuning. Produk ini dirancang untuk mendukung pola hidup sehat dengan kandungan nutrisi yang lengkap dan telah bersertifikat Halal serta terdaftar di BPOM.</p>
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Kapan waktu terbaik mengonsumsi Sahih Bodyfeed?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Sahih Bodyfeed dapat dikonsumsi kapan saja, namun waktu terbaik adalah saat sarapan pagi atau sebagai pengganti camilan sehat di siang hari. Anda juga bisa mengonsumsinya sebelum tidur untuk membantu pencernaan.</p>
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Apakah Sahih Bodyfeed cocok untuk semua usia?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Ya, Sahih Bodyfeed aman dikonsumsi untuk semua usia mulai dari anak-anak hingga lansia. Namun, untuk anak di bawah 3 tahun, sebaiknya konsultasikan terlebih dahulu dengan dokter.</p>
                    </div>
                </div>
                
                <!-- FAQ 4 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Berapa lama produk bisa bertahan setelah dibuka?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Setelah dibuka, produk dapat bertahan hingga 3 bulan jika disimpan dalam wadah tertutup rapat dan di tempat yang sejuk dan kering. Pastikan tutup jar selalu tertutup rapat setelah digunakan.</p>
                    </div>
                </div>
                
                <!-- FAQ 5 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Apakah ada efek samping mengonsumsi Sahih Bodyfeed?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 5 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Sahih Bodyfeed terbuat dari bahan-bahan alami dan aman dikonsumsi. Namun, jika Anda memiliki alergi terhadap kacang-kacangan atau biji-bijian tertentu, sebaiknya periksa komposisi produk terlebih dahulu atau konsultasikan dengan dokter.</p>
                    </div>
                </div>
                
                <!-- FAQ 6 -->
                <div class="bg-white rounded-xl overflow-hidden shadow">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-bold text-lg">Bagaimana cara menyimpan Sahih Bodyfeed?</span>
                        <i class="las la-angle-down text-2xl transition-transform" :class="openFaq === 6 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-8 pb-6">
                        <p class="text-gray-700">Simpan Sahih Bodyfeed di tempat yang sejuk, kering, dan terhindar dari sinar matahari langsung. Pastikan tutup jar selalu tertutup rapat untuk menjaga kesegaran dan kualitas produk.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
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
                        <li class="flex items-center"><i class="las la-envelope mr-2"></i>halo@sahihbodyfeed</li>
                        <li class="flex items-center"><i class="las la-map-marker mr-2"></i>Jl. Pengayoman, Kompleks Pasar Segar Lantai 1 Blok KBD 11, Kota Makassar, Sulawesi Selatan 90231</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 SAHIH BODYFEED. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/082123373732" target="_blank" class="fixed bottom-8 right-8 bg-green text-white w-16 h-16 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform z-50">
        <i class="lab la-whatsapp text-4xl"></i>
    </a>
    
</body>
</html>
