<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahih Bodyfeed - Premium Multigrain Cereal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Instagram Embed -->
    <script async src="//www.instagram.com/embed.js"></script>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-1': '#FCE7C8',
                        'brand-2': '#B1C29E',
                        'brand-3': '#FADA7A',
                        'brand-4': '#F0A04B',
                        'brand-dark': '#2D3436',
                    },
                    fontFamily: {
                        'sans': ['Outfit', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        html, body {
            overflow-x: hidden !important;
            width: 100%;
            position: relative;
        }

        html {
            scroll-behavior: smooth;
        }

        [x-cloak] { display: none !important; }
        
        .bg-cream { background-color: var(--brand-1); }
        
        .hero-gradient {
            background: linear-gradient(135deg, #FCE7C8 0%, #FFF9F2 100%);
        }
        
        .btn-brand {
            background-color: #84994F; /* Sage-ish green from original but more premium */
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-brand:hover {
            background-color: #6d7f42;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(132, 153, 79, 0.4);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        section {
            scroll-margin-top: 100px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #F0A04B;
            border-radius: 10px;
        }

        /* Responsive Instagram Embeds Fix */
        .instagram-media {
            min-width: auto !important;
            width: 100% !important;
            margin: 0 !important;
        }

        iframe.instagram-media {
            min-width: auto !important;
            width: 100% !important;
        }
        
        /* Custom side panel scroll indicator */
        .indicator-line {
            width: 2px;
            height: 60px;
            background: rgba(0,0,0,0.1);
            position: relative;
        }
        .indicator-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 30%;
            background: #F0A04B;
        }
    </style>
</head>
<body class="antialiased text-brand-dark bg-white" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = window.pageYOffset > 50">

    <!-- Header / Nav -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" 
         :class="scrolled ? 'bg-white/80 backdrop-blur-lg py-4 shadow-sm' : 'bg-transparent py-8'">
        <div class="container mx-auto px-6 lg:px-12 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img src="{{ asset('sahihbodyfeed.png') }}" alt="Logo" class="transition-all duration-500" :class="scrolled ? 'h-10 lg:h-16' : 'h-14 lg:h-24'">
            </a>
            
            <!-- Menu Center -->
            <div class="hidden lg:flex items-center space-x-12">
                <a href="#home" class="text-sm font-bold tracking-widest uppercase hover:text-brand-4 transition">Home</a>
                <a href="#product" class="text-sm font-bold tracking-widest uppercase hover:text-brand-4 transition">Product</a>
                <a href="#testimonials" class="text-sm font-bold tracking-widest uppercase hover:text-brand-4 transition">Testimonials</a>
            </div>
            
            <!-- Right Icons -->
            <div class="flex items-center space-x-6">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('agent.dashboard') }}" class="bg-brand-4 text-white px-6 py-2.5 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-brand-4/90 transition shadow-lg shadow-brand-4/20">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="bg-brand-4 text-white px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-brand-4/90 transition shadow-lg shadow-brand-4/20">Login</a>
                @endauth
                
                <button @click="mobileMenu = true" class="lg:hidden text-2xl">
                    <i class="las la-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenu" x-transition x-cloak class="fixed inset-0 z-[60] bg-brand-dark/95 backdrop-blur-sm flex flex-col items-center justify-center text-white space-y-8">
        <button @click="mobileMenu = false" class="absolute top-8 right-8 text-4xl">
            <i class="las la-times"></i>
        </button>
        <a href="#home" @click="mobileMenu = false" class="text-2xl font-serif">Home</a>
        <a href="#product" @click="mobileMenu = false" class="text-2xl font-serif">Product</a>
        <a href="#testimonials" @click="mobileMenu = false" class="text-2xl font-serif">Testimonials</a>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient min-h-screen relative overflow-hidden flex items-center pt-32 lg:pt-20 pb-16 lg:pb-0">
        <!-- Left Vertical Indicator (Desktop) -->
        <div class="hidden lg:flex absolute left-12 top-1/2 -translate-y-1/2 flex-col items-center space-y-4">
            <span class="text-[10px] font-bold tracking-widest uppercase rotate-90 origin-left mb-8">01</span>
            <div class="indicator-line"></div>
            <span class="text-[10px] font-bold tracking-widest uppercase rotate-90 origin-left mt-8 opacity-30">05</span>
        </div>

        <div class="container mx-auto px-6 lg:px-24">
            <!-- Mobile Headline -->
            <div class="lg:hidden text-center mb-8">
                <h2 class="text-brand-2 font-serif italic text-lg mb-2">Murninya Bijian Pilihan</h2>
                <h1 class="text-4xl font-serif font-black leading-tight">
                    Sahih <span class="text-brand-4">&</span> Bodyfeed
                </h1>
            </div>

            <div class="flex flex-col lg:grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Product Image -->
                <div class="w-3/4 lg:w-full lg:col-span-5 relative mx-auto lg:mx-0">
                    <div class="absolute inset-0 bg-brand-2/20 rounded-full blur-3xl -z-10 scale-150"></div>
                    <img src="{{ asset('images/sahih.png') }}" alt="Sahih Bodyfeed" class="w-full max-w-lg mx-auto drop-shadow-[0_35px_35px_rgba(0,0,0,0.25)]">
                    
                    <!-- Decorative Ingredients (Inspired by reference) -->
                </div>

                <!-- Text Content -->
                <div class="lg:col-span-7 lg:pl-12 text-center lg:text-left">
                    <div class="hidden lg:block">
                        <h2 class="text-brand-2 font-serif italic text-xl lg:text-3xl mb-4">Murninya Bijian Pilihan</h2>
                        <h1 class="text-4xl sm:text-5xl lg:text-8xl font-serif font-black leading-tight mb-8">
                            Sahih <span class="text-brand-4">&</span> <br>Bodyfeed
                        </h1>
                    </div>
                    <p class="text-lg lg:text-xl text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed mb-10">
                        Tubuh butuh asupan yang benar, bukan sekadar kenyang. Sahih Bodyfeed hadir sebagai minuman multigrain premium yang menggabungkan kacang-kacangan, biji-bijian, dan labu kuning.
                    </p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-8 mb-16">
                        <div class="flex items-center space-x-4">
                            <span class="text-3xl lg:text-4xl font-serif font-bold text-brand-4">Rp 295.000</span>
                            <span class="text-xs lg:text-sm font-bold text-gray-400 uppercase tracking-widest">/ Kaleng</span>
                        </div>
                    </div>

                    <!-- Hero Stats -->
                    <div class="grid grid-cols-3 gap-4 lg:gap-8 pt-12 border-t border-black/5">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Ingredients</p>
                            <p class="text-base lg:text-lg font-serif font-bold">100% Organik</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Benefit</p>
                            <p class="text-base lg:text-lg font-serif font-bold">Pencernaan</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Volume</p>
                            <p class="text-base lg:text-lg font-serif font-bold">25ml / Saji</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Main Benefits Section -->
    <section class="py-16 lg:py-24 bg-brand-1/20">
        <div class="container mx-auto px-6 lg:px-24 text-center">
            <p class="text-[10px] font-bold text-brand-4 uppercase tracking-[0.4em] mb-4">Core Benefits</p>
            <h2 class="text-3xl lg:text-6xl font-serif font-bold mb-12 lg:mb-16">Manfaat Utama</h2>
            
            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white/50 backdrop-blur-sm p-8 lg:p-10 rounded-[30px] lg:rounded-[40px] border border-brand-2/30 group hover:bg-brand-2 transition-all duration-500 text-left">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl bg-brand-2/10 flex items-center justify-center text-brand-2 group-hover:bg-white group-hover:text-brand-2 transition-all">
                            <i class="las la-heart text-2xl lg:text-3xl"></i>
                        </div>
                        <span class="text-[10px] font-bold group-hover:text-white/80 transition uppercase tracking-widest text-gray-400">Gula Darah</span>
                    </div>
                    <h5 class="font-serif font-bold text-xl lg:text-2xl mb-4 group-hover:text-white transition">Menstabilkan Kadar Gula</h5>
                    <p class="text-sm lg:text-base text-gray-600 group-hover:text-white/70 transition leading-relaxed">Membantu menjaga keseimbangan insulin tubuh dengan nutrisi biji-bijian pilihan.</p>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-8 lg:p-10 rounded-[30px] lg:rounded-[40px] border border-brand-4/20 group hover:bg-brand-4 transition-all duration-500 text-left">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl bg-brand-4/10 flex items-center justify-center text-brand-4 group-hover:bg-white group-hover:text-brand-4 transition-all">
                            <i class="las la-shield-alt text-2xl lg:text-3xl"></i>
                        </div>
                        <span class="text-[10px] font-bold group-hover:text-white/80 transition uppercase tracking-widest text-gray-400">Kolestrol</span>
                    </div>
                    <h5 class="font-serif font-bold text-xl lg:text-2xl mb-4 group-hover:text-white transition">Menurunkan Kolestrol</h5>
                    <p class="text-sm lg:text-base text-gray-600 group-hover:text-white/70 transition leading-relaxed">Kaya akan serat larut yang efektif membantu kesehatan jantung dan pembuluh darah.</p>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-8 lg:p-10 rounded-[30px] lg:rounded-[40px] border border-black/5 group hover:bg-brand-dark transition-all duration-500 text-left">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl bg-brand-dark/10 flex items-center justify-center text-brand-dark group-hover:bg-white group-hover:text-brand-dark transition-all">
                            <i class="las la-spa text-2xl lg:text-3xl"></i>
                        </div>
                        <span class="text-[10px] font-bold group-hover:text-white/80 transition uppercase tracking-widest text-gray-400">Pencernaan</span>
                    </div>
                    <h5 class="font-serif font-bold text-xl lg:text-2xl mb-4 group-hover:text-white transition">Maag & GERD</h5>
                    <p class="text-sm lg:text-base text-gray-600 group-hover:text-white/70 transition leading-relaxed">Memberikan lapisan pelindung pada lambung dan memperbaiki sistem pencernaan secara alami.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Spotlight Section (Focus on Quality) -->
    <section class="py-24 lg:py-32 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6 lg:px-24">
            <div class="text-center mb-16 lg:mb-24">
                <p class="text-[10px] font-bold text-brand-4 uppercase tracking-[0.4em] mb-4">Premium Quality</p>
                <h2 class="text-4xl lg:text-6xl font-serif font-bold max-w-3xl mx-auto leading-tight">
                    Kebaikan Alam dalam <br>Setiap Takaran
                </h2>
                <p class="text-gray-500 mt-6 max-w-2xl mx-auto text-lg">Kami percaya bahwa kesehatan dimulai dari apa yang Anda konsumsi. Sahihbodyfeed diformulasikan dengan bahan-bahan murni pilihan tanpa kompromi.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Ingredient 1 -->
                <div class="bg-brand-1/30 p-8 rounded-[40px] border border-brand-1 hover:border-brand-4 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="las la-seedling text-3xl text-brand-4"></i>
                    </div>
                    <h3 class="text-xl font-serif font-bold mb-4">Multigrain Pilihan</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Campuran biji-bijian utuh yang kaya akan mikronutrisi untuk memenuhi kebutuhan energi harian Anda.</p>
                </div>

                <!-- Ingredient 2 -->
                <div class="bg-brand-1/30 p-8 rounded-[40px] border border-brand-1 hover:border-brand-2 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="las la-sun text-3xl text-brand-2"></i>
                    </div>
                    <h3 class="text-xl font-serif font-bold mb-4">Labu Kuning</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sumber Betakaroten alami yang sangat baik untuk kesehatan mata dan memperkuat sistem imun tubuh.</p>
                </div>

                <!-- Ingredient 3 -->
                <div class="bg-brand-1/30 p-8 rounded-[40px] border border-brand-1 hover:border-brand-3 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="las la-apple-alt text-3xl text-brand-3"></i>
                    </div>
                    <h3 class="text-xl font-serif font-bold mb-4">Tinggi Serat</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Membantu sistem pencernaan bekerja lebih optimal dan memberikan efek kenyang lebih lama (diet-friendly).</p>
                </div>

                <!-- Ingredient 4 -->
                <div class="bg-brand-1/30 p-8 rounded-[40px] border border-brand-1 hover:border-brand-dark transition-all duration-500 group">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="las la-leaf text-3xl text-brand-dark"></i>
                    </div>
                    <h3 class="text-xl font-serif font-bold mb-4">100% Alami</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Tanpa bahan pengawet, pemanis buatan, atau pewarna kimia. Aman dikonsumsi rutin setiap hari.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Composition (Large Graphic) -->
    <section id="product" class="bg-brand-1 py-20 lg:py-32 overflow-hidden">
        <div class="container mx-auto px-6 lg:px-24">
            <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <div>
                    <h2 class="text-4xl lg:text-7xl font-serif font-bold mb-12 leading-tight">Keajaiban dalam <br><span class="italic text-brand-4 text-3xl lg:text-7xl">Satu Gelas</span></h2>
                    <div class="space-y-8 lg:space-y-12">
                        <div class="flex gap-6 lg:gap-8 group">
                            <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 group-hover:bg-brand-4 group-hover:text-white transition-all shadow-sm">
                                <i class="las la-leaf text-2xl lg:text-3xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl lg:text-2xl font-serif font-bold mb-2 lg:mb-3">Multigrain Premium</h4>
                                <p class="text-sm lg:text-base text-gray-600 leading-relaxed">Kombinasi 12 jenis biji-bijian pilihan untuk nutrisi maksimal harian Anda.</p>
                            </div>
                        </div>
                        <div class="flex gap-8 group">
                            <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 group-hover:bg-brand-4 group-hover:text-white transition-all shadow-sm">
                                <i class="las la-sun text-3xl"></i>
                            </div>
                            <div>
                                <h4 class="text-2xl font-serif font-bold mb-3">Labu Kuning Pilihan</h4>
                                <p class="text-gray-600 leading-relaxed">Mengandung betakaroten tinggi untuk daya tahan tubuh dan kesehatan mata.</p>
                            </div>
                        </div>
                        <div class="flex gap-8 group">
                            <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 group-hover:bg-brand-4 group-hover:text-white transition-all shadow-sm">
                                <i class="las la-tint text-3xl"></i>
                            </div>
                            <div>
                                <h4 class="text-2xl font-serif font-bold mb-3">Rendah Glikemik</h4>
                                <p class="text-gray-600 leading-relaxed">Aman dikonsumsi penderita diabetes dan membantu program diet sehat.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-white/40 blur-3xl -z-10"></div>
                    <img src="{{ asset('images/product-compositions.svg') }}" class="w-full drop-shadow-2xl">
                </div>
            </div>
        </div>
    </section>



    <!-- Testimonials -->
    <section id="testimonials" class="py-20 lg:py-32 bg-cream/30 relative overflow-hidden" x-data="{ 
        scrollLeft() { 
            const slider = this.$refs.testimonialSlider;
            slider.scrollBy({ left: -(slider.clientWidth), behavior: 'smooth' });
        },
        scrollRight() { 
            const slider = this.$refs.testimonialSlider;
            slider.scrollBy({ left: (slider.clientWidth), behavior: 'smooth' });
        }
    }">
        <div class="container mx-auto px-6 lg:px-24 relative z-10">
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-24 items-center">
                <!-- Title and Controls -->
                <div class="w-full lg:w-1/3 text-center lg:text-left">
                    <p class="text-[10px] font-bold text-brand-4 uppercase tracking-[0.4em] mb-4">Stories</p>
                    <h2 class="text-4xl lg:text-5xl font-serif font-bold mb-8">Apa Kata <br class="hidden lg:block">Mereka?</h2>
                    <div class="flex justify-center lg:justify-start space-x-4 mb-8 lg:mb-0">
                        <button @click="scrollLeft()" class="w-12 h-12 rounded-full border border-black/10 flex items-center justify-center bg-white/50 backdrop-blur hover:bg-black hover:text-white transition active:scale-95 shadow-sm">
                            <i class="las la-arrow-left"></i>
                        </button>
                        <button @click="scrollRight()" class="w-12 h-12 rounded-full border border-black/10 flex items-center justify-center bg-white/50 backdrop-blur hover:bg-black hover:text-white transition active:scale-95 shadow-sm">
                            <i class="las la-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Slider Container -->
                <div class="w-full lg:w-2/3 overflow-hidden">
                    <div x-ref="testimonialSlider" class="flex overflow-x-auto gap-4 lg:gap-8 pb-8 custom-scrollbar snap-x snap-mandatory touch-pan-x no-scrollbar">
                        <!-- Reel 1 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DVxPF9iD42Q/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                        <!-- Reel 2 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DU7UNmOj74p/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                        <!-- Reel 3 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DTKDXdEj2L3/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                        <!-- Reel 4 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DCK_j02hH4E/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                        <!-- Reel 5 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DDD9j52zvdZ/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                        <!-- Reel 6 -->
                        <div class="snap-center shrink-0 w-[280px] sm:w-[350px] bg-white rounded-[30px] overflow-hidden shadow-sm border border-black/5 p-2 relative">
                            <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/DCOCMewJZs-/" data-instgrm-version="14"></blockquote>
                            <div class="absolute inset-x-0 bottom-0 h-32 z-20 lg:hidden"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Logos / Partners -->
    <section class="py-24 bg-white border-b border-black/5">
        <div class="container mx-auto px-6">
            <p class="text-center text-[10px] font-bold text-gray-300 uppercase tracking-[0.5em] mb-16">Trusted By Institutions</p>
            <div class="flex flex-wrap justify-center items-center gap-12 lg:gap-24 opacity-40 grayscale hover:grayscale-0 transition-all">
                <div class="text-2xl font-black font-serif italic tracking-tighter">BPOM RI</div>
                <div class="text-2xl font-black font-serif italic tracking-tighter">HALAL MUI</div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-brand-dark text-white py-24">
        <div class="container mx-auto px-6 lg:px-24">
            <div class="grid lg:grid-cols-12 gap-16 mb-24">
                <div class="lg:col-span-5">
                    <img src="{{ asset('sahihbodyfeed.png') }}" class="h-16 mb-8 object-contain">
                    <p class="text-gray-400 text-lg leading-relaxed max-w-sm mb-12">
                        Memberikan yang terbaik dari alam untuk kesehatan keluarga Anda. Minuman multigrain premium untuk hidup yang lebih berkualitas.
                    </p>
                    <div class="flex space-x-6">
                        <a href="https://www.instagram.com/sahihbodyfeed?igsh=MWxoZnF3bDJpazRmbQ==" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-brand-4 hover:border-brand-4 transition">
                            <i class="lab la-instagram text-2xl"></i>
                        </a>
                        <a href="https://www.tiktok.com/@sahih.bodyfeed?_r=1&_t=ZS-95zsUpRRjcq" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-brand-4 hover:border-brand-4 transition">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M448 209.91a210.06 210.06 0 0 1 -122.77-39.25V349.38a162.55 162.55 0 1 1 -162.55-162.54 161.63 161.63 0 0 1 71.89 16.54v92.59a70.07 70.07 0 1 0 -70.07 70.07 70.07 70.07 0 0 0 70.07-70.07V0h93.54a107.41 107.41 0 0 0 107.42 107.41v102.5z"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/082123373732" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-brand-4 hover:border-brand-4 transition">
                            <i class="lab la-whatsapp text-2xl"></i>
                        </a>
                    </div>
                </div>
                
                <div class="lg:col-span-2">
                    <h5 class="text-xs font-bold uppercase tracking-widest mb-8">Sitemap</h5>
                    <ul class="space-y-4 text-gray-400">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#product" class="hover:text-white transition">Product</a></li>
                        <li><a href="#testimonials" class="hover:text-white transition">Testimonials</a></li>
                    </ul>
                </div>
                
                <div class="lg:col-span-5">
                    <h5 class="text-xs font-bold uppercase tracking-widest mb-8">Contact Us</h5>
                    <ul class="space-y-6 text-gray-400">
                        <li class="flex items-start gap-4">
                            <i class="las la-phone text-brand-4 text-xl"></i>
                            <span class="font-bold">+62 821-2337-3732</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i class="las la-envelope text-brand-4 text-xl"></i>
                            <span class="font-bold">halo@sahihbodyfeed</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i class="las la-map-marker text-brand-4 text-xl"></i>
                            <span class="leading-relaxed">Jl. Pengayoman, Kompleks Pasar Segar Lantai 1 Blok KBD 29, Kota Makassar, Sulawesi Selatan 90231</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-gray-500 text-sm">&copy; 2025 Sahih Bodyfeed. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp -->
    <a href="https://wa.me/082123373732" target="_blank" class="fixed bottom-8 right-8 z-50 group">
        <div class="absolute inset-0 bg-green-500 rounded-full blur-xl group-hover:blur-2xl transition-all opacity-40"></div>
        <div class="relative bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-all">
            <i class="lab la-whatsapp text-4xl"></i>
        </div>
    </a>

</body>
</html>
