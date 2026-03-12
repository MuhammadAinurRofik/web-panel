<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tighter uppercase">
                    Monitoring Server
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1 italic">
                    Live Deployment Control Center
                </p>
            </div>
            
            <form action="{{ url()->current() }}" method="GET" class="flex bg-white shadow-xl shadow-gray-200/50 rounded-lg overflow-hidden border border-gray-100 group focus-within:border-indigo-500 transition-all">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari mahasiswa..." 
                    class="border-none focus:ring-0 text-xs px-5 py-2.5 w-64 font-bold text-gray-600 tracking-widest">
                
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
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="space-y-8">
                @forelse ($projects as $project)
                    @php $isOnline = $project->status === 'active'; @endphp
                    <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-xl hover:shadow-indigo-100/40 transition-all duration-500 overflow-hidden group">
                        <div class="p-8 md:p-10">
                            <div class="grid grid-cols-1 lg:grid-cols-10 gap-10 items-start">
                                
                                <div class="lg:col-span-4 min-w-0">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="h-14 w-14 shrink-0 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 flex items-center justify-center text-white shadow-lg font-black text-xl">
                                            {{ strtoupper(substr($project->author_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-lg font-black text-gray-900 break-words leading-tight uppercase tracking-tighter">
                                                {{ $project->author_name ?? 'Tanpa Nama' }}
                                            </h3>
                                            <div class="flex items-center gap-2 mt-2">
                                                <p class="text-[11px] font-bold text-gray-400 break-all uppercase tracking-wider">{{ $project->user->email ?? '' }}</p>
                                                <span class="text-[9px] font-black px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded border border-indigo-100 uppercase italic">
                                                   {{ $project->project_type }}
                                                </span>
                                                <span class="text-[9px] font-black px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase">
                                                    @if($project->project_type == 'Flask')
                                                        Python {{ $project->python_version }}
                                                    @else
                                                        PHP {{ $project->php_version }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 flex items-center justify-between shadow-inner">
                                        <div class="min-w-0">
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Project Name</p>
                                            <h4 class="text-sm font-black text-gray-800 leading-snug break-words uppercase">{{ $project->project_name }}</h4>
                                        </div>
                                        <a href="http://{{ $project->subdomain }}" target="_blank" title="{{ $project->subdomain }}" 
                                           class="h-8 w-8 shrink-0 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-indigo-600 hover:bg-black hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="lg:col-span-3 lg:border-l border-gray-100 lg:pl-10">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 block">Database Credentials</label>
                                    @if($project->db_name)
                                        <div class="space-y-3" x-data="{ showPass: false }">
                                            <div>
                                                <span class="text-[10px] font-black text-indigo-400 uppercase">DB_NAME</span>
                                                <code class="text-xs font-bold text-gray-700 bg-gray-50 px-2 py-1.5 rounded border border-gray-100 block mt-1 break-all">{{ $project->db_name }}</code>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-black text-indigo-400 uppercase">DB_USER</span>
                                                <code class="text-xs font-bold text-gray-700 bg-gray-50 px-2 py-1.5 rounded border border-gray-100 block mt-1 break-all">{{ $project->db_user ?? $project->db_name }}</code>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-black text-indigo-400 uppercase">DB_PASSWORD</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <code class="flex-1 text-xs font-bold text-gray-700 bg-gray-50 px-2 py-1.5 rounded border border-gray-100 truncate">
                                                        <span x-show="!showPass">••••••••</span>
                                                        <span x-show="showPass">{{ $project->db_password }}</span>
                                                    </code>
                                                    <button @click="showPass = !showPass" class="text-gray-400 hover:text-indigo-600">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-24 flex items-center justify-center border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                            <span class="text-[9px] font-black text-gray-300 uppercase italic tracking-widest">No Database</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="lg:col-span-3 lg:border-l border-gray-100 lg:pl-10 flex flex-col gap-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block text-center lg:text-left">System Controls</label>
                                    
                                    <div class="flex items-center justify-between px-5 py-2.5 bg-white border-2 border-gray-100 rounded-lg shadow-sm mb-1">
                                        <span id="badge-{{ $project->project_id }}" 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border 
                                            {{ $isOnline ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200' }}">
                                            
                                            <span id="text-{{ $project->project_id }}" class="text-[9px] font-black uppercase tracking-widest">
                                                {{ $isOnline ? 'Active / Online' : 'Suspended / Offline' }}
                                            </span>
                                        </span>

                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" 
                                                {{ $isOnline ? 'checked' : '' }} 
                                                onchange="toggleProjectStatus({{ $project->project_id }})">
                                            
                                            <div class="relative w-8 h-4 bg-gray-200 rounded-full peer 
                                                        peer-checked:after:translate-x-full after:content-[''] 
                                                        after:absolute after:top-[2px] after:start-[2px] 
                                                        after:bg-white after:rounded-full after:h-3 after:w-3 
                                                        after:transition-all peer-checked:bg-indigo-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('filemanager.index', ['project_id' => $project->project_id]) }}" 
                                           class="flex items-center justify-center gap-2 py-3 bg-gray-900 hover:bg-black text-white rounded-xl transition-all text-[10px] font-black uppercase tracking-widest shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                            </svg>
                                            FILE
                                        </a>
                                        <button onclick="showLogs({{ $project->project_id }})" 
                                                class="flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all text-[10px] font-black uppercase tracking-widest shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            LOG
                                        </button>
                                    </div>
                                    
                                    <button onclick="showSystemError({{ $project->project_id }})" 
                                            class="w-full flex items-center justify-center gap-2 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition-all text-[10px] font-black uppercase tracking-widest shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg>
                                        ERROR
                                    </button>

                                    <div x-data="{ showDeleteModal: false }">
                                        <button type="button" @click="showDeleteModal = true" 
                                                class="w-full flex items-center justify-center gap-2 py-3 bg-red-600 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-md transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            HAPUS PROJECT
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

                                                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Konfirmasi Terminasi</h3>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed px-2">
                                                        Project milik <span class="text-red-500 underline">{{ $project->author_name }}</span> akan dihapus permanen.
                                                    </p>

                                                    <div class="w-full p-4 bg-gray-50 rounded-lg border border-gray-100 text-left mb-6">
                                                        <ul class="text-[9px] font-black text-gray-500 uppercase tracking-wider space-y-1">
                                                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> File /var/www/ dihapus</li>
                                                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Database dihapus</li>
                                                        </ul>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-3">
                                                        <button type="button" @click="showDeleteModal = false" 
                                                                class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-100 transition-all">
                                                            BATAL
                                                        </button>
                                                        <form action="{{ route('admin.projects.destroy', $project->project_id) }}" method="POST">
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
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-32 flex flex-col items-center justify-center bg-white border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-[11px] font-black text-gray-300 uppercase tracking-[0.5em]">No Live Applications Detected</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="logModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeLogs()"></div>
            
            <div class="relative bg-white rounded-lg overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-300">
                <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Deployment Detail & Console</h3>
                    <button onclick="closeLogs()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 border-r pr-4 transition-all duration-300" id="timeline-container">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">Status History</h4>
                            <div id="logContent" class="space-y-4 max-h-[400px] overflow-y-auto">
                                </div>
                        </div>

                        <div class="md:col-span-2" id="terminal-container">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">Installation Console (install_log.txt)</h4>
                            <div class="relative">
                                <div class="absolute top-0 left-0 right-0 h-6 bg-gray-800 rounded-t-lg flex items-center px-3 space-x-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                </div>
                                <pre id="fileLogContent" 
                                    class="w-full h-[400px] bg-black text-green-400 p-8 pt-10 text-[11px] font-mono overflow-y-auto rounded-lg shadow-inner border border-gray-700 whitespace-pre-wrap"></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 text-right">
                    <button type="button" onclick="closeLogs()" class="px-5 py-2 bg-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-300 transition shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div id="errorModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" onclick="closeErrorModal()"></div>
            
            <div class="relative bg-gray-900 rounded-lg overflow-hidden shadow-2xl transform transition-all sm:max-w-4xl sm:w-full border border-gray-700">
                <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-red-400 font-mono italic">System Error Real-time Log (PHP-FPM/Nginx)</h3>
                    <button onclick="closeErrorModal()" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <div class="p-0">
                    <pre id="errorContent" class="p-6 text-[11px] leading-relaxed text-gray-300 font-mono bg-black overflow-x-auto max-h-[500px] whitespace-pre-wrap"></pre>
                </div>
                <div class="bg-gray-800 px-4 py-3 text-right">
                    <button type="button" onclick="closeErrorModal()" class="px-4 py-2 bg-gray-700 text-white text-xs font-bold rounded hover:bg-gray-600">Tutup Console</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = searchInput ? searchInput.closest('form') : null;
            let timeout = null;

            // 1. Kembalikan Fokus & Posisi Kursor setelah reload
            if (searchInput && localStorage.getItem('searchFocusActive') === 'true') {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
                localStorage.removeItem('searchFocusActive');
            }

            // 2. Auto-Submit saat mengetik (Debouncing)
            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        localStorage.setItem('searchFocusActive', 'true');
                        searchForm.submit();
                    }, 700); 
                });
            }
        });

        let logPollingInterval = null; // Variabel global untuk menyimpan interval

        function showLogs(projectId) {
            const modal = document.getElementById('logModal');
            const timelineContent = document.getElementById('logContent');
            const timelineContainer = document.getElementById('timeline-container');
            const terminalContent = document.getElementById('fileLogContent');
            const terminalContainer = document.getElementById('terminal-container');
            const modalDialog = modal.querySelector('.relative.bg-white');

            // 1. Reset Tampilan & Bersihkan interval lama jika masih berjalan
            if (logPollingInterval) clearInterval(logPollingInterval);
            modal.classList.remove('hidden');
            timelineContent.innerHTML = '<p class="text-center py-4 text-gray-400 animate-pulse text-xs">Connecting to server...</p>';
            
            const performFetch = () => {
                fetch(`/projects/${projectId}/logs`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            timelineContent.innerHTML = '<p class="text-center text-gray-500 py-4 text-xs italic">Belum ada history.</p>';
                            clearInterval(logPollingInterval);
                            return;
                        }

                        const projectType = data[0].project_type; // 'Flask', 'Laravel', 'PHP Native', atau 'HTML Static'
                        const fileContent = data[0].file_content || "";

                        // 2. LOGIKA TAMPILAN ADAPTIF
                        // Terminal hanya muncul untuk Flask & Laravel
                        if (projectType === 'Flask' || projectType === 'Laravel') {
                            // MODE LEBAR (Tampilkan Terminal)
                            terminalContainer.classList.remove('hidden');
                            modalDialog.classList.replace('sm:max-w-lg', 'sm:max-w-4xl');
                            timelineContainer.classList.replace('md:col-span-3', 'md:col-span-1');
                            timelineContainer.classList.add('border-r');

                            // Isi konten terminal
                            terminalContent.textContent = fileContent ? fileContent : '# Menyiapkan environment instalasi...';
                            terminalContent.scrollTop = terminalContent.scrollHeight;
                        } else {
                            // MODE RINGKAS (Sembunyikan Terminal untuk Native/HTML)
                            terminalContainer.classList.add('hidden');
                            modalDialog.classList.replace('sm:max-w-4xl', 'sm:max-w-lg');
                            timelineContainer.classList.replace('md:col-span-1', 'md:col-span-3');
                            timelineContainer.classList.remove('border-r');
                            
                            // Karena Native/HTML tidak punya file_log yang terus update, 
                            // kita matikan polling setelah data timeline pertama kali muncul.
                            clearInterval(logPollingInterval);
                        }

                        // 3. Render Timeline (History dari Database) - Selalu Muncul
                        timelineContent.innerHTML = data.map(log => `
                            <div class="border-l-2 ${log.status === 'Success' || log.status === 'Processing' ? 'border-indigo-400' : 'border-red-500'} pl-4 py-1 relative mb-4">
                                <div class="absolute -left-[5px] top-2 w-2 h-2 rounded-full ${log.status === 'Success' ? 'bg-indigo-400' : (log.status === 'Processing' ? 'bg-yellow-400' : 'bg-red-500')}"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold uppercase tracking-tighter text-gray-400">${log.timestamp || 'Baru saja'}</span>
                                    <span class="text-xs font-black ${log.status === 'Success' ? 'text-indigo-600' : 'text-red-600'}">${log.process}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 mt-1 italic">${log.message || '-'}</p>
                            </div>
                        `).join('');

                        // 4. Berhenti Polling jika proses selesai (Untuk Flask/Laravel)
                        if (fileContent.includes("DEPLOYMENT SELESAI")) {
                            clearInterval(logPollingInterval);
                        }
                    })
                    .catch(error => {
                        console.error("Polling Error:", error);
                    });
            };

            // Jalankan segera dan set interval
            performFetch();
            logPollingInterval = setInterval(performFetch, 2000);
        }

        // Fungsi untuk menutup modal & WAJIB menghentikan polling
        function closeLogs() {
            const modal = document.getElementById('logModal');
            modal.classList.add('hidden');
            
            if (logPollingInterval) {
                clearInterval(logPollingInterval);
                logPollingInterval = null;
                console.log("Modal ditutup. Polling dihentikan.");
            }
        }

        function showSystemError(projectId) {
            const modal = document.getElementById('errorModal');
            const content = document.getElementById('errorContent');
            
            modal.classList.remove('hidden');
            content.innerHTML = '<span class="text-blue-400 animate-pulse"># Connecting to server logs...</span>';

            fetch(`/admin/projects/${projectId}/error-log`) // Pastikan route ini sesuai dengan Controller Anda
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Beri warna merah pada teks Fatal Error agar mudah dibaca
                        const formattedLog = data.data.replace(/PHP Fatal error/g, '<span class="text-red-500 font-bold underline">PHP Fatal error</span>');
                        content.innerHTML = formattedLog;
                    } else {
                        content.innerHTML = `<span class="text-amber-500"># System Message: ${data.message}</span>`;
                    }
                })
                .catch(error => {
                    content.innerHTML = '<span class="text-red-500 font-bold"># GAGAL: Tidak dapat mengakses file log. Periksa izin sudoers www-data.</span>';
                });
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        function toggleProjectStatus(projectId) {
            fetch(`/admin/projects/${projectId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById(`badge-${projectId}`);
                    const text = document.getElementById(`text-${projectId}`);
                    
                    if(data.newStatus === 'active') {
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-green-100 text-green-800 border-green-200';
                        text.innerText = 'Active / Online';
                    } else {
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-red-100 text-red-800 border-red-200';
                        text.innerText = 'Suspended / Offline';
                    }
                }
            })
            .catch(() => {
                alert('Gagal menghubungi server.');
                location.reload();
            });
        }
    </script>
</x-app-layout>