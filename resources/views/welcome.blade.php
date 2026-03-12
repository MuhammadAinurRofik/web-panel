<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web Project Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            /* Menyesuaikan variabel warna ke palet Orange */
            --color-orange-50: #fff7ed;
            --color-orange-100: #ffedd5;
            --color-orange-600: #ea580c;
            --color-orange-700: #c2410c;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden min-h-screen flex flex-col">

    <header class="w-full max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
        <div class="flex items-center gap-2 group">
            <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-100 group-hover:rotate-6 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xl font-black uppercase tracking-tighter">Web<span class="text-orange-600">Panel</span></span>
        </div>

        @if (Route::has('login'))
            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-[10px] font-black uppercase tracking-widest px-6 py-2.5 bg-black text-white rounded-xl hover:bg-orange-600 transition-all">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-orange-600 transition-colors">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-[10px] font-black uppercase tracking-widest px-6 py-2.5 bg-orange-600 text-white rounded-xl shadow-lg shadow-orange-100 hover:bg-black transition-all">Sign Up</a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <main class="flex-grow flex items-center justify-center px-6">
        <div class="max-w-4xl text-center">
            <div class="inline-block px-4 py-1.5 mb-8 text-[10px] font-black uppercase tracking-[0.3em] bg-orange-100 text-orange-700 rounded-full border border-orange-200">
                Automated Deployment System
            </div>
            
            <h1 class="text-6xl md:text-8xl font-black text-gray-900 leading-[0.9] tracking-tighter uppercase mb-8">
                Build. Deploy. <br> <span class="text-orange-600 italic">Live</span>.
            </h1>
            
            <p class="text-base md:text-lg text-gray-500 font-medium mb-12 max-w-xl mx-auto leading-relaxed">
                Platform mandiri untuk mahasiswa mengelola proyek tugas akhir. Unggah source code Anda dan saksikan proyek Anda online dalam hitungan detik.
            </p>
        </div>
    </main>

    <footer class="w-full py-10 text-center border-t border-gray-100 mt-auto">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.4em]">
            &copy; 2026 — Web Project Development Panel — version 1.0
        </p>
    </footer>

</body>
</html>