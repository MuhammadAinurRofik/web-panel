<x-app-layout>
    <style>
        /* Mengunci scroll utama browser agar tidak ada double scrollbar */
        html, body { 
            overflow: hidden !important; 
            height: 100vh;
        }
    </style>

    <x-slot name="header">
        <div>
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Server Remote Terminal') }}
            </h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Akses konsol sistem dan manajemen infrastruktur secara langsung</p>
        </div>
    </x-slot>

    <div class="py-6 h-[calc(100vh-150px)] overflow-hidden">
        <div class="max-w-7xl mx-auto h-full sm:px-6 lg:px-8 flex flex-col">
            
            <div class="bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-800 h-full flex flex-col" x-data="terminalHandler()">
                
                <div class="bg-gray-800 px-4 py-2.5 flex items-center justify-between border-b border-gray-700 shrink-0">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500 shadow-inner"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500 shadow-inner"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500 shadow-inner"></div>
                    </div>
                    <div class="text-[10px] font-mono text-gray-400 uppercase">
                        Active Session: /var/www
                    </div>
                </div>

                <div id="terminal-screen" class="flex-1 overflow-y-auto p-5 font-mono text-[13px] leading-relaxed scrollbar-thin scrollbar-thumb-gray-700 bg-black/40">
                    <div class="text-gray-600 mb-4 italic text-[11px]">
                        Web Terminal v1.0.0 | Connected as: {{ Auth::user()->name }}
                    </div>
                    
                    <template x-for="(log, index) in logs" :key="index">
                        <div class="mb-4">
                            <div class="flex items-start gap-2">
                                <span class="text-emerald-500 font-bold shrink-0">admin@root:~$</span>
                                <span class="text-white break-all" x-text="log.cmd"></span>
                            </div>
                            <pre class="mt-1 text-gray-300 whitespace-pre-wrap pl-4 border-l border-gray-800 ml-2" x-text="log.res"></pre>
                        </div>
                    </template>

                    <div x-show="isExecuting" class="flex items-center gap-2 text-indigo-400 italic text-xs ml-2">
                        <span class="animate-pulse">_</span> Menjalankan...
                    </div>
                </div>

                <div class="bg-gray-800 p-4 border-t border-gray-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="text-emerald-500 font-bold font-mono text-sm">~$</span>
                        <input type="text" 
                               x-model="inputCommand" 
                               @keydown.enter="sendPayload"
                               class="flex-1 bg-transparent border-none outline-none text-white font-mono text-sm focus:ring-0 p-0"
                               placeholder="Type command here..."
                               autofocus>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        function terminalHandler() {
            return {
                inputCommand: '',
                logs: [],
                isExecuting: false,
                
                sendPayload() {
                    // Fitur clear screen
                    if (this.inputCommand.trim() === 'clear') {
                        this.logs = [];
                        this.inputCommand = '';
                        return;
                    }

                    if (!this.inputCommand.trim() || this.isExecuting) return;

                    let cmd = this.inputCommand;
                    this.isExecuting = true;
                    this.inputCommand = '';

                    fetch("{{ route('admin.terminal.execute') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ command: cmd })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.logs.push({ cmd: cmd, res: data.output });
                        // Auto-scroll tetap di bawah
                        this.$nextTick(() => {
                            const screen = document.getElementById('terminal-screen');
                            screen.scrollTop = screen.scrollHeight;
                        });
                    })
                    .finally(() => this.isExecuting = false);
                }
            }
        }
    </script>
</x-app-layout>