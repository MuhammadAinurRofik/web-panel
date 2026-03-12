<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('System Backup Manager') }}
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Arsip repositori asli mahasiswa dan arsip file .sql</p>
            </div>

            <form action="{{ route('admin.backups.index') }}" method="GET" id="searchForm" class="flex bg-white shadow-xl shadow-gray-200/50 rounded-lg overflow-hidden border border-gray-100">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                       placeholder="Cari nama mahasiswa..." 
                       autocomplete="off"
                       class="border-none focus:ring-0 text-xs px-5 py-2.5 w-64 font-bold text-gray-600">
                
                <button type="submit" class="bg-indigo-600 hover:bg-black text-white px-6 py-2.5 text-[10px] font-black transition-all uppercase tracking-widest">
                    CARI
                </button>
                
                @if(request('search'))
                    <a href="{{ route('admin.backups.index') }}" class="bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-2.5 text-xs flex items-center border-l border-gray-100 transition-colors">
                        ✕
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" id="backupContainer">
            
            @if(empty($backups))
                <div class="bg-white rounded-xl shadow-xl shadow-gray-200/50 border border-gray-100 p-20 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-gray-300 uppercase tracking-[0.5em] italic">
                        @if(request('search'))
                            Tidak ditemukan backup untuk "{{ request('search') }}"
                        @else
                            Belum ada folder backup tersedia
                        @endif
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($backups as $backup)
                <div class="bg-white rounded-xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden group hover:border-indigo-500 transition-all duration-300" x-data="{ showDeleteFolder: false }">
                    
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white rounded-lg shadow-sm text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-tight truncate w-32">{{ $backup['author_name'] }}</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $backup['size'] }} MB</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('admin.backups.downloadFolder', $backup['author_name']) }}" class="p-2 bg-gray-900 text-white rounded-lg hover:bg-indigo-600 transition-all shadow-md shadow-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                            <button @click="showDeleteFolder = true" class="p-2 bg-white border border-gray-200 text-red-500 rounded-lg hover:bg-red-50 hover:border-red-200 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 01 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-5 space-y-3">
                        @foreach($backup['files'] as $file)
                        <div class="flex items-center justify-between group/file" x-data="{ showDeleteFile: false }">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span class="text-[10px] font-bold text-gray-600 truncate uppercase tracking-tighter">{{ $file }}</span>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover/file:opacity-100 transition-opacity">
                                <a href="{{ route('admin.backups.downloadFile', [$backup['author_name'], $file]) }}" class="text-indigo-500 hover:text-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </a>
                                <button @click="showDeleteFile = true" class="text-red-400 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 01 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="showDeleteFile" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showDeleteFile = false"></div>
                                    <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100">
                                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded flex items-center justify-center mx-auto mb-4 shadow-inner text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Hapus File Backup</h3>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">Menghapus {{ $file }} secara permanen.</p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button @click="showDeleteFile = false" class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg">BATAL</button>
                                            <form action="{{ route('admin.backups.destroyFile', [$backup['author_name'], $file]) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-md shadow-red-200">HAPUS</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div x-show="showDeleteFolder" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showDeleteFolder = false"></div>
                            <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100">
                                <div class="w-16 h-16 bg-red-50 text-red-500 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Hapus Semua Backup</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">Arsip milik <span class="text-red-600 underline font-black">{{ $backup['author_name'] }}</span> akan dihapus.</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="showDeleteFolder = false" class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg">BATAL</button>
                                    <form action="{{ route('admin.backups.destroyFolder', $backup['author_name']) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-md shadow-red-200">YA, HAPUS</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            let timeout = null;

            // --- 1. KEMBALIKAN FOKUS KURSOR SETELAH RELOAD ---
            if (searchInput && localStorage.getItem('searchFocus') === 'true') {
                searchInput.focus();
                // Pindahkan kursor ke akhir teks
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
                localStorage.removeItem('searchFocus');
            }

            // --- 2. AUTO-SUBMIT SAAT MENGETIK (DEBOUNCE) ---
            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        // Simpan status fokus agar bisa dikembalikan setelah reload
                        localStorage.setItem('searchFocus', 'true');
                        searchForm.submit();
                    }, 600); // Jeda 0.6 detik setelah berhenti mengetik
                });
            }

            // --- 3. HIGHLIGHT TEXT PADA HASIL PENCARIAN ---
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search');
            
            if (searchTerm && searchTerm.length >= 2) {
                // Targetkan elemen yang berisi nama (h3)
                const nameElements = document.querySelectorAll('h3.truncate');
                const regex = new RegExp(`(${searchTerm})`, 'gi');

                nameElements.forEach(el => {
                    const originalText = el.innerText;
                    if (regex.test(originalText)) {
                        el.innerHTML = originalText.replace(regex, '<mark class="bg-indigo-100 text-indigo-700 px-1 rounded ring-1 ring-indigo-200">$1</mark>');
                    }
                });
            }
        });
    </script>
</x-app-layout>