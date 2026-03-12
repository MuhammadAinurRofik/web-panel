<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-800">
            <div class="bg-white shadow-xl rounded-lg p-6 overflow-hidden">
                @if(session('success'))
                    <div id="alert-success" class="mb-4 flex items-center p-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 shadow-sm transition-all duration-500">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-bold">Berhasil!</span> {{ session('success') }}
                        </div>
                    </div>

                    <script>
                        setTimeout(() => {
                            const alert = document.getElementById('alert-success');
                            if(alert) {
                                alert.style.opacity = '0';
                                setTimeout(() => alert.remove(), 500);
                            }
                        }, 3000);
                    </script>
                @endif

                @if(session('error'))
                    <div class="mb-4 flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-bold">Gagal!</span> {{ session('error') }}
                        </div>
                    </div>
                @endif
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold italic tracking-tighter text-indigo-700 uppercase">Explorer</h2>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">
                        ID Proyek: {{ $projectId ?? 'N/A' }}
                    </span>
                </div>

                <nav class="flex p-3 bg-gray-50 rounded mb-4 font-mono text-xs">
                    <a href="{{ route('filemanager.index') }}?project_id={{ $projectId }}" class="text-indigo-600 font-bold">root</a>
                    @if($currentPath)
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">{{ $currentPath }}</span>
                    @endif
                </nav>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($directories as $dir)
                        @php 
                            $dirName = basename($dir); 
                            $newPath = $currentPath ? $currentPath . '/' . $dirName : $dirName; 
                        @endphp
                        <a href="{{ route('filemanager.index') }}?project_id={{ $projectId }}&path={{ urlencode($newPath) }}" 
                           class="flex items-center p-4 border border-indigo-50 rounded bg-indigo-50/30 hover:bg-indigo-100 transition group shadow-sm">
                            <svg class="w-8 h-8 text-amber-400 mr-3 group-hover:scale-110 transition shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                            </svg>
                            <span class="text-sm font-semibold truncate text-indigo-900">{{ $dirName }}</span>
                        </a>
                    @endforeach

                    @foreach($files as $file)
                        @php 
                            $fileName = $file->getFilename(); 
                            $filePath = $currentPath ? $currentPath . '/' . $fileName : $fileName; 
                        @endphp
                        <div class="flex items-center justify-between p-4 border border-gray-100 rounded hover:shadow-md transition bg-white shadow-sm">
                            <div class="flex items-center overflow-hidden mr-2">
                                <svg class="w-6 h-6 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 truncate">{{ $fileName }}</span>
                            </div>
                            <a href="{{ route('filemanager.edit', ['path' => base64_encode($filePath)]) }}?project_id={{ $projectId }}" 
                               class="text-[10px] font-bold uppercase text-indigo-600 border border-indigo-600 px-2 py-1 rounded hover:bg-indigo-600 hover:text-white transition">
                                Edit
                            </a>
                        </div>
                    @endforeach
                </div>

                @if(count($directories) == 0 && count($files) == 0)
                    <div class="text-center py-10">
                        <p class="text-gray-400 italic text-sm">Folder ini kosong</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>