<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-black uppercase tracking-tighter text-gray-900">
            Daftar Akun
        </h2>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-2">
            Mulai kelola proyek web Anda
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                Nama Lengkap + Nim
            </label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   :value="old('name')" 
                   required 
                   autofocus 
                   placeholder="Contoh: Ahmad Fauzi 22091397001"
                   class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-[10px] " />
        </div>

        <div>
            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                Email
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   :value="old('email')" 
                   required 
                   placeholder="nama@mhs.unesa.ac.id"
                   class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px]" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                    Password
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       placeholder="Masukkan password"
                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                    Konfirmasi
                </label>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       placeholder="Konfirmasi password"
                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 text-sm rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none" />
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px]" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px]" />

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-black text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-orange-100 transition-all transform active:scale-[0.98]">
                Daftar Sekarang
            </button>
        </div>

        <div class="text-center pt-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-orange-600 hover:text-black transition-colors underline underline-offset-4">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>