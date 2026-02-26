<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tighter uppercase">
                    Antrean Deployment
                </h2>
                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Manajemen Proyek Mahasiswa</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-white border-l-4 border-emerald-500 shadow-lg shadow-emerald-100/50 p-3.5 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="bg-emerald-100 p-1.5 rounded-lg text-emerald-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-[13px] font-black text-gray-800 tracking-tight leading-tight">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <div class="space-y-6">
                @forelse ($projects as $project)
                    <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-xl hover:shadow-indigo-100/50 transition-all duration-300 overflow-hidden group">
                        <div class="p-6 md:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-8">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-4 mb-5">
                                        <div class="h-14 w-14 shrink-0 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 flex items-center justify-center text-white shadow-lg">
                                            <span class="text-xl font-black">{{ strtoupper(substr($project->author_name, 0, 1)) }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-lg font-black text-gray-900 break-words leading-tight uppercase tracking-tighter">
                                                {{ $project->author_name ?? 'Tanpa Nama' }}
                                            </h3>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $project->user->email ?? 'NIM Tidak Terdeteksi' }}</span>
                                                <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                                                <span class="text-[9px] font-black px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase">
                                                    {{ $project->project_type }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 relative overflow-hidden">
                                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] rotate-12">
                                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                        </div>

                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Judul Proyek</label>
                                        <h4 class="text-sm font-black text-gray-800 leading-snug break-words">
                                            {{ $project->project_name }}
                                        </h4>
                                        <div class="mt-3 pt-3 border-t border-gray-200/50 flex flex-col gap-1">
                                            <div class="flex items-center text-[10px] text-gray-500 font-mono italic">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                {{ $project->subdomain }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="lg:w-80 shrink-0">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Infrastruktur DB</label>
                                        @if($project->db_name)
                                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-tighter bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">MySQL Active</span>
                                        @endif
                                    </div>

                                    @if($project->db_name)
                                        <div class="bg-white border-2 border-emerald-100 rounded-2xl p-4 space-y-4 shadow-sm relative overflow-hidden">
                                            <div class="flex flex-col">
                                                <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Database Name</p>
                                                <p class="text-xs font-mono font-bold text-emerald-700 break-all leading-relaxed bg-emerald-50/50 p-2 rounded-lg border border-emerald-100/50">
                                                    {{ $project->db_name }}
                                                </p>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="flex flex-col">
                                                    <p class="text-[9px] font-black text-gray-400 uppercase mb-1">User</p>
                                                    <p class="text-xs font-mono font-bold text-gray-700 break-all leading-relaxed">
                                                        {{ $project->db_user }}
                                                    </p>
                                                </div>
                                                <div class="flex flex-col border-l border-gray-100 pl-4">
                                                    <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Password</p>
                                                    <p class="text-xs font-mono font-bold text-gray-700 break-all leading-relaxed">
                                                        {{ $project->db_password }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-full border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center py-10 bg-gray-50/30">
                                            <svg class="w-6 h-6 text-gray-300 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"/><path d="M9 12l2 2 4-4"/></svg>
                                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Tanpa Database</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="lg:w-48 flex flex-col gap-3 shrink-0" x-data="{ showRejectModal: false, showDeployModal: false }">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5 block lg:text-center text-left">Otorisasi</label>
                                    
                                    <button type="button" @click="showDeployModal = true"
                                            class="w-full group/btn bg-indigo-600 hover:bg-black text-white p-3.5 rounded-2xl transition-all duration-300 shadow-xl shadow-indigo-100 flex items-center justify-center gap-2">
                                        <span class="text-[11px] font-black uppercase tracking-widest">Deploy Now</span>
                                        <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </button>

                                    <button @click="showRejectModal = true" 
                                        class="w-full bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-400 p-3.5 rounded-2xl transition-all text-[11px] font-black uppercase tracking-widest">
                                        Tolak Proyek
                                    </button>

                                    <div x-show="showDeployModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showDeployModal = false"></div>
                                            
                                            <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100 transform transition-all">
                                                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                        <path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" clip-rule="evenodd" />
                                                        <path d="M5.26 17.242a.75.75 0 1 0-.897-1.203 5.243 5.243 0 0 0-2.05 5.022.75.75 0 0 0 .625.627 5.243 5.243 0 0 0 5.022-2.051.75.75 0 1 0-1.202-.897 3.744 3.744 0 0 1-3.008 1.51c0-1.23.592-2.323 1.51-3.008Z" />
                                                    </svg>
                                                </div>

                                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Sistem Ready?</h3>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed italic">{{ $project->author_name }}</p>

                                                <div class="w-full p-4 bg-gray-50 rounded-lg border border-gray-100 text-left mb-6">
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 text-center">Prosedur Otomatis:</p>
                                                    <ul class="text-[9px] font-black text-gray-600 uppercase tracking-wider space-y-1">
                                                        <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span> Alokasi Folder /var/www/</li>
                                                        <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span> Setup Nginx & Database</li>
                                                    </ul>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <button @click="showDeployModal = false" class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-100 transition-all">BATAL</button>
                                                    <form action="{{ route('admin.projects.approve', $project->project_id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-black transition-all shadow-md shadow-indigo-100">LAUNCH</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="showRejectModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm" @click="showRejectModal = false"></div>
                                            
                                            <div class="relative bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full text-center border border-gray-100 transform transition-all">
                                                <div class="w-16 h-16 bg-red-50 text-red-500 rounded flex items-center justify-center mx-auto mb-4 shadow-inner">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                </div>

                                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-tighter mb-1">Reject Report</h3>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 leading-relaxed">Feedback: {{ $project->author_name }}</p>

                                                <form action="{{ route('admin.projects.reject', $project->project_id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="reason" required 
                                                        class="w-full border-2 border-gray-50 rounded-lg p-3 text-[11px] font-bold focus:border-red-500 focus:ring-0 transition-colors mb-4 bg-gray-50 uppercase tracking-wider placeholder:text-gray-300" 
                                                        placeholder="ALASAN PENOLAKAN..."></textarea>
                                                    
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <button type="button" @click="showRejectModal = false" class="py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-100 transition-all">BATAL</button>
                                                        <button type="submit" class="w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-black transition-all shadow-md shadow-red-200">SEND</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-32 flex flex-col items-center justify-center bg-white border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-xs font-black text-gray-300 uppercase tracking-[0.5em]">Belum Ada Antrean</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>