<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-2xl font-black uppercase tracking-tighter text-gray-900">
            Selamat Datang
        </h2>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-2">
            Masuk ke Akun Web Panel Anda
        </p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                Alamat Email
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   :value="old('email')" 
                   required 
                   autofocus 
                   placeholder="nama@mhs.unesa.ac.id"
                   class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm font-bold rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-2 px-1">
                <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[9px] font-black text-orange-600 uppercase tracking-tighter hover:text-black transition-colors">
                        Lupa Password?
                    </a>
                @endif
            </div>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   placeholder="Masukkan password"
                   class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold" />
        </div>

        <div class="flex items-center px-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded-lg border-gray-200 text-orange-600 focus:ring-0 transition-all cursor-pointer">
                <span class="ms-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-gray-600 transition-colors">
                    Ingat Sesi Saya
                </span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-black text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-orange-100 transition-all transform active:scale-[0.98]">
                Masuk Ke Sistem
            </button>
        </div>

        <div class="text-center pt-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                Belum terdaftar? 
                <a href="{{ route('register') }}" class="text-orange-600 hover:text-black transition-colors underline underline-offset-4">
                    Buat Akun Baru
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>