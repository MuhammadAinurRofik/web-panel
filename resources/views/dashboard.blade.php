<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto">
            <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tighter uppercase">
                {{ __('Dashboard Mahasiswa - Monitoring Project') }}
            </h2>
        </div>
    </x-slot>

    <div class="pt-4 pb-8" x-data="{ openUpload: false }"> <!-- Mengurangi padding-top untuk header -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"> <!-- Meningkatkan max-width untuk tabel lebih lebar -->
            
            @if(session('success'))
                <div class="mb-4 bg-white border-l-4 border-emerald-500 shadow-lg shadow-emerald-100/50 p-3.5 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="bg-emerald-100 p-1.5 rounded-lg text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-[13px] font-black text-gray-800 tracking-tight">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-white border-l-4 border-red-500 shadow-lg shadow-red-100/50 p-3.5 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="bg-red-100 p-1.5 rounded-lg text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-[13px] font-black text-gray-800 tracking-tight">{{ session('error') }}</p>
                </div>
            @endif

            @foreach(Auth::user()->notifications()->where('status', 'unread')->latest()->get() as $notif)
                <div class="mb-3 bg-white border-l-4 border-indigo-500 shadow-lg shadow-indigo-100/50 p-3.5 rounded-xl flex justify-between items-center animate-in fade-in slide-in-from-top-4 duration-500 group">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-100 p-1.5 rounded-lg text-indigo-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-[13px] font-black text-gray-800 tracking-tight leading-tight">
                            {{ $notif->message }}
                        </p>
                    </div>
                    
                    <form action="{{ route('notifications.read', $notif->notif_id) }}" method="POST" class="ml-4 shrink-0">
                        @csrf
                        <button type="submit" class="text-[9px] font-black text-gray-400 hover:text-indigo-600 uppercase tracking-[0.1em] transition-all duration-200">
                            Tutup
                        </button>
                    </form>
                </div>
            @endforeach

            <div class="bg-white rounded-lg shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden w-full">
                <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter leading-none">Informasi Proyek Tugas Akhir</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Kelola dan pantau seluruh repositori Anda</p>
                    </div>
                    
                    <button @click="openUpload = true" 
                            class="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-black text-[10px] font-black uppercase tracking-widest text-white rounded-lg transition-all duration-300 shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Project
                    </button>
                </div>
                
                <div class="p-0">
                    @if($projects->isEmpty())
                        <div class="py-20 flex flex-col items-center justify-center">
                            <div class="bg-gray-50 p-6 rounded-full mb-4 text-gray-200">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Belum ada proyek terdaftar</p>
                        </div>
                    @else
                    
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Nama Project</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Tipe</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Status</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Akses</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Kredensial</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($projects as $project)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-200 group">
                                        <td class="px-6 py-5 min-w-[300px]">
                                            <span class="text-xs font-black text-gray-900 uppercase tracking-tight break-words block leading-snug">
                                                {{ $project->project_name }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded border border-indigo-100 uppercase">
                                                    {{ $project->project_type }}
                                                </span>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">
                                                    @if($project->project_type == 'Flask')
                                                        Python {{ $project->python_version }}
                                                    @else
                                                        PHP {{ $project->php_version }}
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-5 text-center">
                                            @php
                                                $statusConfig = [
                                                    'active' => ['bg' => 'bg-emerald-50 border-emerald-100', 'text' => 'text-emerald-600'],
                                                    'pending' => ['bg' => 'bg-amber-50 border-amber-100', 'text' => 'text-amber-600'],
                                                    'inactive' => ['bg' => 'bg-red-50 border-red-100', 'text' => 'text-red-600']
                                                ];
                                                $config = $statusConfig[$project->status] ?? ['bg' => 'bg-gray-50 border-gray-100', 'text' => 'text-gray-600'];
                                            @endphp
                                            <span class="inline-flex items-center justify-center px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $config['bg'] }} {{ $config['text'] }}">
                                                {{ $project->status }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex justify-center">
                                                @if($project->status !== 'pending' && $project->subdomain)
                                                    <a href="http://{{ $project->subdomain }}" target="_blank" 
                                                    class="inline-flex items-center justify-center gap-1.5 text-[10px] font-mono font-bold text-indigo-600 hover:text-black transition-colors italic bg-indigo-50/50 px-3 py-1.5 rounded border border-indigo-100/50 group">
                                                        <svg class="w-3.5 h-3.5 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                            <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        LINK
                                                    </a>
                                                @else
                                                    <span class="text-[10px] font-black text-gray-300 uppercase italic tracking-widest">
                                                        —
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-5 text-center">
                                            @if($project->db_name)
                                                <div class="inline-block text-left bg-gray-50 rounded px-3 py-2 border border-gray-100 shadow-inner group-hover:bg-white transition-all">
                                                    <div class="space-y-1 font-mono text-xs font-bold text-gray-600 italic leading-tight">
                                                        <p><span class="text-indigo-400 not-italic uppercase tracking-tighter">DB_NAME:</span> {{ $project->db_name }}</p>
                                                        <p><span class="text-indigo-400 not-italic uppercase tracking-tighter">DB_USER:</span> {{ $project->db_user }}</p>
                                                        <p><span class="text-indigo-400 not-italic uppercase tracking-tighter">PASSWORD:</span> {{ $project->db_password }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-[10px] font-black text-gray-300 uppercase italic tracking-widest">No DB</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex flex-col gap-2 w-32 mx-auto" x-data="{ showDelete: false }">
                                                
                                                @if($project->status !== 'pending')
                                                <a href="{{ route('filemanager.index', ['project_id' => $project->project_id]) }}" 
                                                   class="flex items-center justify-center gap-2 py-3 bg-gray-900 hover:bg-black text-[10px] font-black uppercase tracking-widest text-white rounded-lg transition-all shadow-md">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                    FILES
                                                </a>
                                                @endif    
                                            
                                                <button onclick="showLogs({{ $project->project_id }})" 
                                                        class="flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-black text-[10px] font-black uppercase tracking-widest text-white rounded-lg transition-all shadow-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    LOGS
                                                </button>

                                                <button @click="showDelete = true" 
                                                        class="flex items-center justify-center gap-2 py-3 bg-red-600 hover:bg-black text-[10px] font-black uppercase tracking-widest text-white rounded-lg transition-all shadow-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    HAPUS
                                                </button>

                                                <div x-show="showDelete" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
                                                    <div class="flex items-center justify-center min-h-screen px-4">
                                                        <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showDelete = false"></div>
                                                        <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100">
                                                            <div class="w-16 h-16 bg-red-50 text-red-500 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008z" />
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Konfirmasi Hapus</h3>
                                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">Folder dan Database akan dihapus permanen.</p>
                                                            <div class="grid grid-cols-2 gap-3">
                                                                <button @click="showDelete = false" class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-100 transition-all">BATAL</button>
                                                                <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-black transition-all shadow-md shadow-red-200">YA, HAPUS</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upload Modal - Compact Square -->
        <div x-show="openUpload" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm transition-opacity" 
                @click="openUpload = false"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="openUpload"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm z-10 overflow-hidden border border-gray-200">
                    
                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="bg-indigo-600 px-4 py-3">
                            <h3 class="text-sm font-bold text-white text-center uppercase tracking-widest">Konfigurasi Proyek Baru</h3>
                        </div>
                        
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 mb-1 uppercase tracking-wider">1. NAMA PROYEK</label>
                                <input type="text" name="project_name" required placeholder="Contoh: Web Penjualan"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200 outline-none font-medium">
                            </div>

                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-3" x-data="{ runtime: 'php' }">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="shrink-0">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Runtime</label>
                                    </div>

                                    <div class="flex bg-gray-200/50 p-1 rounded-lg w-full max-w-[140px]">
                                        <button type="button" @click="runtime = 'php'" 
                                            :class="runtime === 'php' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500'"
                                            class="flex-1 text-[9px] font-black py-1 rounded-md transition-all uppercase">PHP</button>
                                        <button type="button" @click="runtime = 'python'" 
                                            :class="runtime === 'python' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500'"
                                            class="flex-1 text-[9px] font-black py-1 rounded-md transition-all uppercase">Python</button>
                                        <input type="hidden" name="runtime_type" :value="runtime">
                                    </div>

                                    <div class="w-24">
                                        <select name="php_version" x-show="runtime === 'php'" 
                                            class="w-full bg-white border-gray-200 rounded-lg text-[11px] font-bold text-indigo-600 py-1 px-2 focus:ring-0">
                                            <option value="8.4">8.4</option>
                                            <option value="8.3">8.3</option>
                                            <option value="8.2">8.2</option>
                                        </select>

                                        <select name="python_version" x-show="runtime === 'python'" x-cloak
                                            class="w-full bg-white border-gray-200 rounded-lg text-[11px] font-bold text-indigo-600 py-1 px-2 focus:ring-0">
                                            <option value="3.12">3.12</option>
                                            <option value="3.11">3.11</option>
                                            <option value="3.10">3.10</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-[9px] text-gray-400 italic text-center">Auto-detection framework aktif setelah upload.</p>
                                </div>
                            </div>

                            <div x-data="{ needDatabase: false }" class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200 transition-all hover:border-indigo-300">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 bg-indigo-100 rounded-lg">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.1.9 2 2 2h12a2 2 0 002-2V7M4 7a2 2 0 012-2h12a2 2 0 012 2M4 7l8 5 8-5M12 12V4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <label for="db_toggle" class="text-[11px] font-bold text-gray-700 uppercase tracking-tight">Butuh Database?</label>
                                            <p class="text-[9px] text-gray-500 italic">Centang jika proyek Anda menggunakan MySQL</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" id="db_toggle" x-model="needDatabase" 
                                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                                </div>

                                <div x-show="needDatabase" 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 space-y-3 shadow-inner">
                                    
                                    <label class="block text-[10px] font-extrabold text-emerald-600 mb-1 uppercase tracking-wider text-center">
                                        Konfigurasi Kredensial
                                    </label>

                                    @php 
                                        $nameParts = explode(' ', trim(Auth::user()->name));
                                        $twoWords = isset($nameParts[1]) ? $nameParts[0] . '_' . $nameParts[1] : $nameParts[0];
                                        $namePrefix = Str::limit(Str::slug($twoWords, '_'), 15, '') . '_'; 
                                    @endphp

                                    <div>
                                        <label class="block text-[9px] font-bold text-emerald-700 mb-1">NAMA DATABASE</label>
                                        <div class="flex items-center group">
                                            <span class="bg-emerald-100 border border-r-0 border-emerald-200 px-2.5 py-1.5 text-[10px] text-emerald-700 rounded-l-lg font-mono font-bold">db_</span>
                                            <input type="text" id="db_name_input" name="db_name_input" value="{{ $namePrefix }}"
                                                onkeydown="forcePrefix(event, '{{ $namePrefix }}')"
                                                oninput="checkPrefix(this, '{{ $namePrefix }}')"
                                                class="flex-1 px-2.5 py-1.5 text-[11px] border border-emerald-200 rounded-r-lg focus:ring-0 focus:border-emerald-500 outline-none font-mono bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-bold text-emerald-700 mb-1">USERNAME DATABASE</label>
                                        <div class="flex items-center group">
                                            <span class="bg-emerald-100 border border-r-0 border-emerald-200 px-2.5 py-1.5 text-[10px] text-emerald-700 rounded-l-lg font-mono font-bold">user_</span>
                                            <input type="text" id="db_user_input" name="db_user_input" value="{{ $namePrefix }}"
                                                onkeydown="forcePrefix(event, '{{ $namePrefix }}')"
                                                oninput="checkPrefix(this, '{{ $namePrefix }}')"
                                                class="flex-1 px-2.5 py-1.5 text-[11px] border border-emerald-200 rounded-r-lg focus:ring-0 focus:border-emerald-500 outline-none font-mono bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-bold text-emerald-700 mb-1">PASSWORD DATABASE</label>
                                        <input type="password" name="db_password_input" placeholder="Isi untuk password kustom"
                                            class="w-full px-2.5 py-1.5 text-[11px] border border-emerald-200 rounded-lg focus:ring-0 focus:border-emerald-500 outline-none font-mono bg-white">
                                    </div>

                                    <p class="text-[9px] text-emerald-600 italic leading-tight text-center px-2">
                                        Format otomatis: <b>db_{{ $namePrefix }}...</b>
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 mb-1 uppercase tracking-wider">3. SOURCE CODE (ZIP)</label>
                                <div class="relative">
                                    <input id="file_zip" name="file_zip" type="file" accept=".zip" required class="hidden" onchange="updateFileName(this, 'file-name-zip')">
                                    <label for="file_zip" class="flex items-center justify-between w-full px-3 py-2.5 border border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group">
                                        <span id="file-name-zip" class="text-[10px] text-gray-500 truncate italic">Pilih file .zip</span>
                                        <div class="p-1 bg-white rounded shadow-sm border border-gray-200 group-hover:border-indigo-300">
                                            <svg class="w-3 h-3 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 mb-1 uppercase tracking-wider">4. DATABASE SQL (OPSIONAL)</label>
                                <div class="relative">
                                    <input id="sql_file" name="sql_file" type="file" accept=".sql" class="hidden" onchange="updateFileName(this, 'file-name-sql')">
                                    <label for="sql_file" class="flex items-center justify-between w-full px-3 py-2.5 border border-indigo-100 border-dashed rounded-lg cursor-pointer bg-indigo-50/30 hover:bg-indigo-50 transition-all group">
                                        <span id="file-name-sql" class="text-[10px] text-indigo-400 truncate italic font-medium">Unggah file .sql</span>
                                        <div class="p-1 bg-white rounded shadow-sm border border-indigo-200 group-hover:border-indigo-400">
                                            <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-5 py-4 flex flex-row gap-2 border-t border-gray-100">
                            <button type="button" @click="openUpload = false" class="flex-1 py-2 bg-white text-gray-500 text-[10px] font-bold rounded-lg border border-gray-200 hover:bg-gray-100 transition-all uppercase tracking-widest">
                                BATAL
                            </button>
                            <button type="submit" class="flex-1 py-2 bg-indigo-600 text-white text-[10px] font-bold rounded-lg shadow-md hover:bg-indigo-700 transition-all uppercase tracking-widest">
                                UNGGAH PROYEK
                            </button>
                        </div>
                    </form>
                </div>
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

    <script>
        // Mencegah penghapusan prefix (muhammad_ainur_)
        function forcePrefix(e, prefix) {
            if (e.keyCode === 8 && e.target.selectionStart <= prefix.length) {
                e.preventDefault();
            }
        }

        function checkPrefix(input, prefix) {
            if (!input.value.startsWith(prefix)) {
                input.value = prefix;
            }
        }

        // Reset input ke prefix saat modal dibuka
        document.addEventListener('click', function (e) {
            // Cari tombol yang memiliki atribut @click openUpload
            if (e.target.closest('button') && e.target.closest('button').innerText.includes('Upload Project')) {
                const prefix = "{{ $namePrefix }}";
                setTimeout(() => {
                    const dbInput = document.getElementById('db_name_input');
                    const userInput = document.getElementById('db_user_input');
                    if(dbInput) dbInput.value = prefix;
                    if(userInput) userInput.value = prefix;
                }, 100);
            }
        });

        // Fungsi pembantu untuk nama file label (tetap diperlukan)
        function updateFileName(input, targetId) {
            const fileName = input.files[0] ? input.files[0].name : 'Pilih file';
            document.getElementById(targetId).textContent = fileName;
        }

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
        
        function updateFileName(input, targetId) {
            const fileNameDisplay = document.getElementById(targetId);
            
            if (input.files.length > 0) {
                // Update teks dengan nama file
                fileNameDisplay.textContent = input.files[0].name;
                
                // Hapus gaya italic/warna pudar agar teks nama file terlihat jelas
                fileNameDisplay.classList.remove('italic', 'text-gray-500', 'text-indigo-400');
                fileNameDisplay.classList.add('text-gray-900', 'font-bold', 'not-italic');
            } else {
                // Jika batal pilih, kembalikan ke teks default
                if (targetId === 'file-name-zip') {
                    fileNameDisplay.textContent = 'Pilih file proyek .zip';
                    fileNameDisplay.classList.add('text-gray-500', 'italic');
                } else {
                    fileNameDisplay.textContent = 'Unggah file .sql jika ada';
                    fileNameDisplay.classList.add('text-indigo-400', 'italic');
                }
                fileNameDisplay.classList.remove('text-gray-900', 'font-bold', 'not-italic');
            }
        }
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Button focus states */
        button:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            ring-width: 2px;
        }
    </style>
</x-app-layout>