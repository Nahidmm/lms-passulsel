@extends('layouts.app')

@section('title', 'Knowledge Base AI (RAG)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Knowledge Base AI</h1>
        <p class="text-text-secondary text-sm mt-1">Kelola dokumen PDF untuk dianalisis oleh AI Assistant</p>
    </div>
    <button onclick="openModal('add-dokumen-modal')" class="btn btn-primary text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2 shadow-xs">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Upload Dokumen
    </button>
</div>

@if(session('success'))
<div class="bg-success/10 text-success border border-success/20 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
    <i data-lucide="check-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="bg-danger/10 text-danger border border-danger/20 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
    <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
    <p class="text-sm font-medium">{{ session('error') }}</p>
</div>
@endif

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Judul / Nama Dokumen</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Tipe</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">File PDF</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Data RAG (Chunks)</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($dokumens as $doc)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-4 px-6">
                        <p class="text-sm font-medium text-text-primary">{{ $doc->judul }}</p>
                        <p class="text-xs text-text-secondary mt-0.5">Diunggah: {{ $doc->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @if($doc->tipe)
                        <span class="px-2.5 py-1 bg-accent/10 text-accent rounded-lg text-xs font-semibold">
                            {{ $doc->tipe }}
                        </span>
                        @else
                        <span class="text-text-secondary text-xs">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-primary hover:underline text-sm flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Lihat PDF
                        </a>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 bg-success/10 text-success rounded-lg text-xs font-semibold">
                            {{ $doc->chunks_count }} Potongan Teks
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <form action="{{ route('admin.kelola-akses.dokumen-ai.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen beserta seluruh data AI yang dipelajari darinya?');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-text-secondary hover:text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus Dokumen">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-text-secondary">
                        <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="database" class="w-8 h-8 opacity-50"></i>
                        </div>
                        <p class="text-sm">Belum ada dokumen AI yang diunggah.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Upload Dokumen -->
<div id="add-dokumen-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div onclick="closeModal('add-dokumen-modal')" style="position:absolute;inset:0;background:rgba(0,0,0,0.55);cursor:pointer;backdrop-filter:blur(4px);"></div>
    <div class="relative bg-[#1a2235] rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.5)] w-full max-w-lg max-h-[90vh] overflow-y-auto z-10 border border-[var(--border)]">
        
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] sticky top-0 bg-[#1a2235] z-10">
            <h3 class="text-lg font-bold text-[var(--text-primary)]">Upload Dokumen AI</h3>
            <button type="button" onclick="closeModal('add-dokumen-modal')" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.kelola-akses.dokumen-ai.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-5 space-y-4">
                <div class="bg-accent/10 border border-accent/20 rounded-xl p-3 flex gap-3 text-sm text-accent mb-2">
                    <i data-lucide="info" class="w-5 h-5 shrink-0"></i>
                    <p>Proses upload mungkin memakan waktu agak lama karena sistem AI perlu membaca teks dan mengubahnya menjadi vektor pengetahuan (Embedding).</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Judul Dokumen <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: SOP Pelayanan Tahanan"
                        class="w-full px-4 py-2 bg-[var(--card)] border border-[var(--border)] shadow-xs border border-[var(--border)] text-[var(--text-primary)] rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none placeholder-white/30">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Tipe Dokumen <span class="text-[var(--text-secondary)] text-xs font-normal">(opsional)</span></label>
                    <input type="text" name="tipe" placeholder="Contoh: SOP, Permen, Kepmen"
                        class="w-full px-4 py-2 bg-[var(--card)] border border-[var(--border)] shadow-xs border border-[var(--border)] text-[var(--text-primary)] rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none placeholder-white/30">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">File PDF <span class="text-rose-500">*</span></label>
                    <input type="file" name="file_pdf" required accept="application/pdf"
                        class="w-full px-4 py-2 bg-[var(--card)] border border-[var(--border)] shadow-xs border border-[var(--border)] text-[var(--text-primary)] rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/20 file:text-blue-400 hover:file:bg-primary/30 cursor-pointer">
                    <p class="text-xs text-[var(--text-secondary)] mt-1">Maksimal 10MB. Hanya file berformat PDF.</p>
                </div>
            </div>
            <div class="p-5 border-t border-[var(--border)] flex justify-end gap-3 bg-[var(--card)] rounded-b-2xl sticky bottom-0">
                <button type="button" onclick="closeModal('add-dokumen-modal')" class="btn btn-secondary text-[var(--text-primary)] px-5 py-2.5 font-medium rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" id="submit-btn" onclick="this.innerHTML='<i data-lucide=\'loader\' class=\'w-4 h-4 animate-spin\'></i> Memproses...';this.classList.add('opacity-75');" class="btn btn-primary text-white px-6 py-2.5 rounded-xl font-medium shadow-xs transition-colors flex items-center gap-2">
                    <i data-lucide="upload" class="w-4 h-4"></i> Upload & Proses AI
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const m = document.getElementById(id);
        if(m) {
            m.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if(m) {
            m.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
</script>
@endsection

