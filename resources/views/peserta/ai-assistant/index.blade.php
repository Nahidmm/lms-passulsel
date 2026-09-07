@extends('layouts.app')

@section('title', 'AI Tutor Disiplin - STRAPSUSPAS')

@section('content')

<div class="h-[calc(100vh-8rem)] flex flex-col bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden relative">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 bg-secondary/30 pointer-events-none"></div>
    
    <!-- Header -->
    <div class="bg-indigo-600 text-white p-4 flex items-center justify-between shrink-0 relative z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/10 backdrop-blur rounded-xl flex items-center justify-center text-white shadow-xs">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="font-bold text-sm">AI Tutor Disiplin ASN</h2>
                <p class="text-[11px] text-white/80">Konsultasi Interaktif Berbasis Regulasi PP 94/2021</p>
            </div>
        </div>
        <div class="text-xs bg-white/10 px-3 py-1 rounded-full font-medium flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div> Online
        </div>
    </div>

    <!-- Chat Area -->
    <div id="chat-container" class="flex-1 p-4 md:p-6 overflow-y-auto relative z-10 space-y-6 scroll-smooth">
        
        <!-- Welcome Message -->
        <div class="flex gap-4">
            <div class="shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white mt-1">
                <i data-lucide="bot" class="w-4 h-4"></i>
            </div>
            <div class="flex-1 bg-secondary rounded-2xl rounded-tl-none p-4 shadow-sm border border-border max-w-[85%] md:max-w-[75%]">
                <p class="text-text-primary text-sm leading-relaxed">
                    Halo, {{ explode(' ', Auth::user()->nama)[0] }}! Saya Asisten Virtual AI Tutor STRAPSUSPAS Kanwil Ditjenpas Sulsel. Ada yang bisa saya bantu terkait regulasi disiplin ASN (PP No. 94 Tahun 2021), tata cara pemeriksaan pelanggaran disiplin, atau penyusunan telaah kasus hari ini?
                </p>
            </div>
        </div>

        <!-- History -->
        @foreach($histories as $msg)
            <div class="flex gap-4 {{ $msg->role === 'user' ? 'flex-row-reverse' : '' }}">
                @if($msg->role === 'assistant')
                    <div class="shrink-0 w-8 h-8 rounded-full bg-primary flex items-center justify-center text-[var(--text-primary)] mt-1">
                        <i data-lucide="bot" class="w-4 h-4"></i>
                    </div>
                @else
                    <div class="shrink-0 w-8 h-8 rounded-full bg-accent flex items-center justify-center text-primary font-bold mt-1 shadow-sm border border-white">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                @endif
                
                <div class="flex-1 {{ $msg->role === 'user' ? 'bg-primary text-white rounded-tr-none' : 'bg-secondary text-text-primary rounded-tl-none border border-border' }} rounded-2xl p-4 shadow-sm max-w-[85%] md:max-w-[75%]">
                    <div class="text-sm leading-relaxed whitespace-pre-wrap">{!! nl2br(e($msg->content)) !!}</div>
                    
                    @if($msg->role === 'assistant' && !empty($msg->references))
                        <div class="mt-4 pt-4 border-t border-border/50">
                            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-2">Sumber Referensi:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($msg->references as $ref)
                                    <a href="{{ Storage::url($ref['file_path']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-lg text-xs text-primary hover:bg-primary/5 hover:border-primary/30 transition-colors">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 shrink-0"></i>
                                        <span class="truncate max-w-[200px]">{{ $ref['judul'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Typing Indicator (Hidden by default) -->
        <div id="typing-indicator" class="flex gap-4 hidden">
            <div class="shrink-0 w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white mt-1">
                <i data-lucide="bot" class="w-4 h-4"></i>
            </div>
            <div class="bg-secondary rounded-2xl rounded-tl-none p-4 shadow-sm border border-border">
                <div class="flex items-center gap-1 h-5">
                    <div class="w-2 h-2 bg-primary/50 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-primary/50 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-primary/50 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-[var(--card)] border border-[var(--border)] shadow-xs border-t border-border shrink-0 relative z-10">
        <form id="chat-form" class="relative flex items-end gap-2">
            @csrf
            <div class="flex-1 bg-secondary rounded-xl border border-border focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all overflow-hidden relative">
                <textarea id="chat-input" rows="1" placeholder="Ketik pertanyaan Anda di sini..." class="w-full bg-transparent border-none outline-none resize-none p-3 max-h-32 min-h-[44px] text-sm text-text-primary scrollbar-hide"></textarea>
            </div>
            <button type="submit" id="send-btn" class="shrink-0 w-11 h-11 bg-primary hover:bg-primary/90 text-white rounded-xl flex items-center justify-center transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i data-lucide="send" class="w-5 h-5 ml-1"></i>
            </button>
        </form>
    </div>

</div>

@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        const typingIndicator = document.getElementById('typing-indicator');

        // Scroll to bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Auto-resize textarea
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if(this.value.trim() === '') {
                this.style.height = '44px';
            }
        });

        // Submit on Enter (prevent default newline), Shift+Enter adds newline
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = chatInput.value.trim();
            if (!message) return;

            // 1. Add user message to UI
            appendUserMessage(message);
            
            // 2. Clear input
            chatInput.value = '';
            chatInput.style.height = '44px';
            
            // 3. Show typing indicator
            typingIndicator.classList.remove('hidden');
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            // Disable input
            chatInput.disabled = true;
            sendBtn.disabled = true;

            // 4. Send to server
            fetch('{{ route("ai.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                typingIndicator.classList.add('hidden');
                
                if (data.success) {
                    appendBotMessage(data.message, data.data.references);
                } else {
                    appendBotMessage("Error: " + (data.message || "Terjadi kesalahan."));
                }
            })
            .catch(error => {
                typingIndicator.classList.add('hidden');
                appendBotMessage("Maaf, terjadi kesalahan koneksi jaringan.");
                console.error('Error:', error);
            })
            .finally(() => {
                chatInput.disabled = false;
                sendBtn.disabled = false;
                chatInput.focus();
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
        });

        function appendUserMessage(text) {
            const initial = '{{ substr(Auth::user()->nama, 0, 1) }}';
            const html = `
                <div class="flex gap-4 flex-row-reverse">
                    <div class="shrink-0 w-8 h-8 rounded-full bg-accent flex items-center justify-center text-primary font-bold mt-1 shadow-sm border border-white">
                        ${initial}
                    </div>
                    <div class="flex-1 bg-primary text-white rounded-2xl rounded-tr-none p-4 shadow-sm max-w-[85%] md:max-w-[75%]">
                        <div class="text-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(text)}</div>
                    </div>
                </div>
            `;
            typingIndicator.insertAdjacentHTML('beforebegin', html);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function appendBotMessage(text, references = null) {
            // Very basic markdown parsing for bold and line breaks
            let formattedText = escapeHtml(text)
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>');

            let referencesHtml = '';
            if (references && references.length > 0) {
                let refLinks = references.map(ref => {
                    return `<a href="/storage/${ref.file_path}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-lg text-xs text-primary hover:bg-primary/5 hover:border-primary/30 transition-colors">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="truncate max-w-[200px]">${escapeHtml(ref.judul)}</span>
                            </a>`;
                }).join('');
                
                referencesHtml = `
                    <div class="mt-4 pt-4 border-t border-border/50">
                        <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-2">Sumber Referensi:</p>
                        <div class="flex flex-wrap gap-2">
                            ${refLinks}
                        </div>
                    </div>
                `;
            }

            const html = `
                <div class="flex gap-4">
                    <div class="shrink-0 w-8 h-8 rounded-full bg-primary flex items-center justify-center text-[var(--text-primary)] mt-1">
                        <i data-lucide="bot" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1 bg-secondary text-text-primary rounded-2xl rounded-tl-none border border-border p-4 shadow-sm max-w-[85%] md:max-w-[75%]">
                        <div class="text-sm leading-relaxed">${formattedText}</div>
                        ${referencesHtml}
                    </div>
                </div>
            `;
            typingIndicator.insertAdjacentHTML('beforebegin', html);
            lucide.createIcons();
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }
    });
</script>
@endpush

