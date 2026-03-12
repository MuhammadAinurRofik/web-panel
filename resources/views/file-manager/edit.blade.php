<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg shadow-2xl overflow-hidden flex flex-col h-[80vh]">
                <div class="bg-gray-800 p-4 flex justify-between items-center border-b border-gray-700">
                    <div class="text-gray-300 font-mono text-sm italic">Editing: {{ $fileName }}</div>
                    <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-white text-xs uppercase tracking-widest">&larr; Tutup</a>
                </div>

                <form action="{{ route('filemanager.save') }}" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $projectId }}">
                    
                    <input type="hidden" name="path" value="{{ $relativePath }}">
                    
                    <textarea name="content" class="flex-1 bg-gray-900 text-green-400 font-mono text-sm p-6 border-none focus:ring-0 resize-none scrollbar-thin scrollbar-thumb-gray-700" spellcheck="false" autofocus>{{ $content }}</textarea>
                    
                    <div class="bg-gray-800 p-4 border-t border-gray-700 flex justify-end gap-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-8 rounded shadow-lg transition uppercase text-xs">
                            Simpan File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>