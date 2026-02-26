<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->usertype == 'admin' ? route('admin.dashboard') : route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="Auth::user()->usertype == 'admin' ? route('admin.dashboard') : route('dashboard') " 
                                :active="Auth::user()->usertype == 'admin' ? request()->routeIs('admin.dashboard') : request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (Auth::user()->usertype == 'admin')
                        
                        <x-nav-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.index')">
                            {{ __('Daftar Antrean') }}
                            @php $pendingCount = \App\Models\Project::where('status', 'pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="ms-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </x-nav-link>

                        <x-nav-link :href="route('admin.projects.active')" :active="request()->routeIs('admin.projects.active')">
                            {{ __('Project Aktif') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.index')">
                            {{ __('Log Aktivitas') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                            {{ __('User') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.backups.index')" :active="request()->routeIs('admin.backups.*')">
                            {{ __('Backup Manager') }}
                            
                            @php 
                                // Menggunakan full namespace agar tidak perlu 'use' di bagian atas file
                                $backupDir = storage_path('app/backups');
                                $backupCount = \Illuminate\Support\Facades\File::exists($backupDir) 
                                    ? count(\Illuminate\Support\Facades\File::directories($backupDir)) 
                                    : 0; 
                            @endphp

                            @if($backupCount > 0)
                                <span class="ms-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700 uppercase tracking-tighter">
                                    {{ $backupCount }}
                                </span>
                            @endif
                        </x-nav-link>

                        <x-nav-link :href="route('admin.terminal')" :active="request()->routeIs('admin.terminal')">
                            {{ __('Terminal') }}
                            <span class="ms-1.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                        </x-nav-link>

                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Notif lonceng -->
                <div class="relative" x-data="{ openNotif: false }">
                    <button @click="openNotif = !openNotif" class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-all duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        
                        @php $unreadCount = Auth::user()->notifications()->where('status', 'unread')->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full shadow-sm">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <div x-show="openNotif" 
                         @click.away="openNotif = false" 
                         x-cloak
                         class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl py-2 z-[60] border border-gray-100 origin-top-right transition-all"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                        
                        <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Notifikasi Terbaru</span>
                            <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full">{{ $unreadCount }} Baru</span>
                        </div>

                        <div class="max-h-80 overflow-y-auto custom-scrollbar">
                            @forelse(Auth::user()->notifications()->latest()->take(10)->get() as $notif)
                                <div class="px-4 py-4 border-b border-gray-50 hover:bg-gray-50 transition duration-150 relative {{ $notif->status == 'unread' ? 'bg-indigo-50/20' : '' }}">
                                    <div class="flex gap-3">
                                        <div class="shrink-0 mt-1">
                                            <div class="w-2 h-2 rounded-full {{ $notif->status == 'unread' ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-gray-300' }}"></div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-800 leading-relaxed {{ $notif->status == 'unread' ? 'font-bold' : '' }}">
                                                {{ $notif->message }}
                                            </p>
                                            <div class="flex justify-between items-center mt-2">
                                                <span class="text-[10px] text-gray-400 font-medium tracking-tight">{{ $notif->created_at->diffForHumans() }}</span>
                                                
                                                @if($notif->status == 'unread')
                                                    <form action="{{ route('notifications.read', $notif->notif_id) }}" method="POST">
                                                        @csrf
                                                        <button class="text-[10px] text-indigo-600 hover:text-indigo-800 font-extrabold uppercase">Tandai Dibaca</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-10 text-center">
                                    <p class="text-xs text-gray-400 italic">Tidak ada notifikasi untuk Anda.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="font-bold">{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden shadow-inner bg-gray-50">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Merapikan scrollbar dropdown notifikasi */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #d1d5db;
    }
    [x-cloak] { display: none !important; }
</style>