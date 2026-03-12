<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tighter uppercase">
                    {{ __('Admin Control Center') }}
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1 italic">Sistem Monitoring & Otorisasi</p>
            </div>
            <div class="mt-2 sm:mt-0">
                <span class="bg-white border border-gray-200 text-gray-500 text-[10px] font-black px-4 py-2 rounded-xl shadow-sm uppercase tracking-widest">
                    {{ now()->format('l, d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden hover:scale-[1.02] transition-all duration-300 group">
                    <div class="p-8 text-center sm:text-left">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-amber-50 rounded-2xl group-hover:bg-amber-500 transition-colors duration-300">
                                <svg class="h-8 w-8 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending</p>
                                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['pending_projects'] }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Antrean Approval</h3>
                        <div class="mt-8">
                            <a href="{{ route('admin.projects.index') }}" class="flex items-center justify-center w-full py-3 bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all">
                                Periksa Antrean
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden hover:scale-[1.02] transition-all duration-300 group">
                    <div class="p-8 text-center sm:text-left">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-emerald-50 rounded-2xl group-hover:bg-emerald-500 transition-colors duration-300">
                                <svg class="h-8 w-8 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Running</p>
                                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['active_projects'] }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Proyek Aktif</h3>
                        <div class="mt-8">
                            <a href="{{ route('admin.projects.active') }}" class="flex items-center justify-center w-full py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all">
                                Monitor Server
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden hover:scale-[1.02] transition-all duration-300 group">
                    <div class="p-8 text-center sm:text-left">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-indigo-50 rounded-2xl group-hover:bg-indigo-600 transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-8 w-8 text-indigo-600 group-hover:text-white transition-colors">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['total_users'] }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Data Mahasiswa</h3>
                        <div class="mt-8">
                            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center w-full py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all">
                                Kelola User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white p-6 rounded-[2rem] shadow-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">CPU Usage</p>
                            <h3 class="text-2xl font-black text-{{ $stats['cpu']['color'] }}-600">{{ $stats['cpu']['usage'] }}%</h3>
                        </div>
                        <div class="p-3 bg-{{ $stats['cpu']['color'] }}-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-{{ $stats['cpu']['color'] }}-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden text-right">
                        <div class="bg-{{ $stats['cpu']['color'] }}-500 h-full transition-all duration-1000" style="width: {{ $stats['cpu']['usage'] }}%"></div>
                    </div>
                    <p class="text-[9px] mt-2 text-gray-400 font-bold italic uppercase tracking-tighter text-right">{{ $stats['cpu']['cores'] }} Cores Active</p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">RAM Memory</p>
                            <h3 class="text-2xl font-black text-{{ $stats['ram']['color'] }}-600">
                                {{ $stats['ram']['used'] }} <span class="text-xs font-bold text-gray-400">/ {{ $stats['ram']['total'] }} GB</span>
                            </h3>
                        </div>
                        <div class="p-3 bg-{{ $stats['ram']['color'] }}-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-{{ $stats['ram']['color'] }}-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-{{ $stats['ram']['color'] }}-500 h-full transition-all duration-1000" style="width: {{ $stats['ram']['percentage'] }}%"></div>
                    </div>
                    <p class="text-[9px] mt-2 text-gray-400 font-bold italic uppercase tracking-tighter">{{ $stats['ram']['percentage'] }}% Terpakai</p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penyimpanan</p>
                            <h3 class="text-2xl font-black text-{{ $stats['disk']['color'] }}-600">
                                {{ $stats['disk']['used'] }} <span class="text-xs font-bold text-gray-400">/ {{ $stats['disk']['total'] }} GB</span>
                            </h3>
                        </div>
                        <div class="p-3 bg-{{ $stats['disk']['color'] }}-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-{{ $stats['disk']['color'] }}-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-{{ $stats['disk']['color'] }}-500 h-full transition-all duration-1000" style="width: {{ $stats['disk']['percentage'] }}%"></div>
                    </div>
                    <p class="text-[9px] mt-2 text-gray-400 font-bold italic uppercase tracking-tighter">{{ $stats['disk']['percentage'] }}% Terisi</p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                <div class="px-8 py-6 bg-white border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-indigo-600 animate-ping"></div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Aktivitas Terbaru</h3>
                    </div>
                    <span class="text-[10px] font-black px-4 py-1.5 bg-gray-100 text-gray-500 rounded-full uppercase tracking-widest">
                        Real-time Audit
                    </span>
                </div>
                
                <div class="p-8">
                    <div class="space-y-4">
                        @forelse($stats['recent_activities'] as $log)
                            <div class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl border border-transparent hover:border-gray-200 hover:bg-white transition-all duration-200 group">
                                <div class="flex items-center space-x-6 min-w-0">
                                    <div class="text-[10px] font-black text-gray-400 bg-white border border-gray-100 px-3 py-2 rounded-lg shadow-sm font-mono group-hover:text-indigo-600 transition-colors">
                                        {{ $log->created_at->format('H:i') }}
                                    </div>
                                    <div class="text-sm truncate">
                                        <span class="font-black text-gray-900 uppercase tracking-tight">{{ $log->user->name }}</span>
                                        <span class="text-gray-400 font-medium mx-2">—</span>
                                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">{{ Str::lower($log->activity) }} proyek</span>
                                    </div>
                                </div>
                                <div class="hidden sm:block">
                                    <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest bg-white px-3 py-1 rounded-full border border-gray-100">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <p class="text-xs font-black text-gray-300 uppercase tracking-[0.3em]">Belum ada aktivitas hari ini</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if(count($stats['recent_activities']) > 0)
                        <div class="mt-10 text-center">
                            <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center px-8 py-3 bg-gray-900 hover:bg-indigo-600 text-white text-[10px] font-black rounded-xl uppercase tracking-[0.2em] transition-all shadow-lg hover:shadow-indigo-200 transform hover:-translate-y-1">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Lihat Seluruh Audit Log
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
    function smoothRefresh() {
        // 1. Ambil URL halaman saat ini
        const currentUrl = window.location.href;

        // 2. Lakukan request ke halaman itu sendiri di latar belakang
        fetch(currentUrl)
            .then(response => response.text())
            .then(html => {
                // 3. Buat "tempel" virtual untuk membaca HTML yang baru diambil
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');

                // 4. Cari elemen pembungkus konten (grid statistik, monitoring, aktivitas)
                // Kita akan mengganti isi dari container utama
                const oldContent = document.querySelector('.max-w-7xl');
                const newContent = newDoc.querySelector('.max-w-7xl');

                if (oldContent && newContent) {
                    // 5. GANTI ISINYA SAJA (Tanpa refresh halaman/browser)
                    oldContent.innerHTML = newContent.innerHTML;
                    console.log('Dashboard diperbarui secara halus pada: ' + new Date().toLocaleTimeString());
                }
            })
            .catch(error => console.warn('Gagal refresh halus:', error));
    }

    // Jalankan setiap 5 detik
    setInterval(smoothRefresh, 5000);
</script>
</x-app-layout>