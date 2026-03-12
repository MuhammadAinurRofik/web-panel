<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Riwayat Aktivitas Panel (Audit Trail)') }}
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Catatan permanen interaksi sistem dan audit trail</p>
            </div>

            <form action="{{ route('admin.logs.index') }}" method="GET" class="flex bg-white shadow-xl shadow-gray-200/50 rounded-lg overflow-hidden border border-gray-100">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari user, aktivitas, atau detail..." 
                       class="border-none focus:ring-0 text-xs px-5 py-2.5 w-64 font-bold text-gray-600">
                <button type="submit" class="bg-indigo-600 hover:bg-black text-white px-6 py-2.5 text-[10px] font-black transition-all uppercase tracking-widest">
                    CARI
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.logs.index') }}" class="bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-2.5 text-xs flex items-center border-l border-gray-100 transition-colors">
                        ✕
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase tracking-widest">Waktu</th>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase tracking-widest">User</th>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase tracking-widest">Aktivitas</th>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase tracking-widest">Detail Pesan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-gray-500">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900 tracking-tight">{{ $log->user->name ?? 'System' }}</div>
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-tighter">{{ $log->user->usertype ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeStyles = match($log->activity) {
                                                'Upload' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'Hapus', 'Hapus Mahasiswa', 'Hapus Proyek' => 'bg-red-50 text-red-600 border-red-100',
                                                'Approve', 'Tambah Mahasiswa' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'Reject' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                default => 'bg-gray-50 text-gray-600 border-gray-100'
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[11px] font-black border tracking-tighter {{ $badgeStyles }}">
                                            {{ $log->activity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-600 leading-relaxed">
                                        {{ $log->description }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <p class="text-xs font-black text-gray-300 uppercase tracking-[0.5em] italic">
                                            @if(request('search'))
                                                Tidak ditemukan hasil untuk "{{ request('search') }}"
                                            @else
                                                Belum ada jejak aktivitas
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = searchInput ? searchInput.closest('form') : null;
            let timeout = null;

            // --- 1. KEMBALIKAN FOKUS & POSISI KURSOR ---
            if (searchInput && localStorage.getItem('searchFocus') === 'true') {
                searchInput.focus();
                
                // Kembalikan posisi kursor ke paling akhir teks
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
                
                // Hapus tanda fokus setelah berhasil dikembalikan
                localStorage.removeItem('searchFocus');
            }

            // --- 2. AUTO-SUBMIT SAAT MENGETIK ---
            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        // Simpan status bahwa input sedang fokus sebelum reload
                        localStorage.setItem('searchFocus', 'true');
                        searchForm.submit();
                    }, 700); // Jeda 0.7 detik (pas untuk mengetik santai)
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