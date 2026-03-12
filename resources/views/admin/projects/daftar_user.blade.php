<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Daftar Mahasiswa Terdaftar') }}
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Kelola data dan akses akun mahasiswa</p>
            </div>
            
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300 shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                TAMBAH MAHASISWA
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-start">
                <form action="{{ url()->current() }}" method="GET" class="flex bg-white shadow-xl shadow-gray-200/50 rounded-lg overflow-hidden border border-gray-100 group focus-within:border-indigo-500 transition-all">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama atau email..." 
                           class="border-none focus:ring-0 text-xs px-5 py-2.5 w-64 md:w-80 font-bold text-gray-600">
                    
                    <button type="submit" class="bg-indigo-600 hover:bg-black text-white px-6 py-2.5 text-[10px] font-black transition-all uppercase tracking-widest">
                        CARI
                    </button>

                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-2.5 text-xs flex items-center border-l border-gray-100 transition-colors">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Nama</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Email</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Proyek</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        
                        <tbody class="bg-white">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-all duration-200 border-b border-gray-100 last:border-0">
                                    <td class="px-6 py-2.5 whitespace-nowrap text-[13px] font-black text-gray-900 tracking-tight">
                                        <div class="{{ $loop->first ? 'pt-3' : '' }} {{ $loop->last ? 'pb-3' : '' }}">
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-2.5 whitespace-nowrap text-[13px] font-bold text-gray-600">
                                        <div class="{{ $loop->first ? 'pt-3' : '' }} {{ $loop->last ? 'pb-3' : '' }}">
                                            {{ $user->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-2.5 whitespace-nowrap text-center">
                                        <div class="{{ $loop->first ? 'pt-3' : '' }} {{ $loop->last ? 'pb-3' : '' }}">
                                            <span class="inline-flex items-center px-3 py-0.5 rounded-full bg-gray-100 border border-gray-200 text-[11px] font-black text-gray-700">
                                                {{ $user->projects()->count() }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-2.5 whitespace-nowrap text-center">
                                        <div class="{{ $loop->first ? 'pt-3' : '' }} {{ $loop->last ? 'pb-3' : '' }}">
                                            <div x-data="{ showDeleteModal: false }">
                                                <button type="button" @click="showDeleteModal = true" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition-all shadow-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Hapus
                                                </button>

                                                <div x-show="showDeleteModal" 
                                                    class="fixed inset-0 z-[100] overflow-y-auto" 
                                                    x-cloak>
                                                    <div class="flex items-center justify-center min-h-screen px-4">
                                                        <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                                                        <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100 transform transition-all">
                                                            
                                                            <div class="w-16 h-16 bg-red-50 text-red-500 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                                </svg>
                                                            </div>

                                                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Konfirmasi Hapus</h3>
                                                            
                                                            <div class="flex flex-col gap-1 items-center mb-6">
                                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                                                                    Anda akan menghapus mahasiswa:
                                                                </p>
                                                                <p class="text-xs font-black text-red-600 uppercase tracking-tighter underline decoration-2">
                                                                    {{ $user->name }}
                                                                </p>
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-3">
                                                                <button type="button" @click="showDeleteModal = false" 
                                                                        class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-100 transition-all">
                                                                    BATAL
                                                                </button>
                                                                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-black transition-all shadow-md shadow-red-200">
                                                                        YA, HAPUS
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center text-gray-400 italic">
                                        <p class="text-xs font-black text-gray-300 uppercase tracking-[0.5em]">
                                            {{ request('search') ? 'Mahasiswa tidak ditemukan' : 'Belum ada mahasiswa' }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('modalTambah').classList.add('hidden')"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                <form action="{{ route('admin.users.store') }}" method="POST" class="p-6">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Mahasiswa Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = searchInput ? searchInput.closest('form') : null;
            let timeout = null;

            // 1. Kembalikan Fokus setelah reload
            if (searchInput && localStorage.getItem('searchFocusUser') === 'true') {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
                localStorage.removeItem('searchFocusUser');
            }

            // 2. Auto-Submit saat mengetik
            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        localStorage.setItem('searchFocusUser', 'true');
                        searchForm.submit();
                    }, 700); 
                });
            }

            // --- 3. HIGHLIGHT TEXT (Opsional) ---
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search');
            if (searchTerm && searchTerm.length >= 2) {
                const contentArea = document.querySelector('tbody');
                if (contentArea) {
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    contentArea.innerHTML = contentArea.innerHTML.replace(regex, '<mark class="bg-yellow-200 text-black rounded px-1">$1</mark>');
                }
            }
        });
    </script>
</x-app-layout>