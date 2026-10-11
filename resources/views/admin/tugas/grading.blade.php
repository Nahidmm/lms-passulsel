@extends('layouts.app')

@section('title', 'Penilaian Tugas: ' . $tugas->judul)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.pelatihan.show', $tugas->pelatihan_id) }}" class="text-text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                {{ $tugas->tipe === 'upload_sertifikat' ? 'Verifikasi Sertifikat' : 'Penilaian Penugasan' }}
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">{{ $tugas->judul }}</h1>
        <p class="text-xs text-text-secondary mt-1">Pelatihan: {{ $tugas->pelatihan->judul }} &bull; Skala Nilai: {{ $tugas->bobot_nilai }}</p>
    </div>
    
    <div class="flex items-center gap-3">
        <div class="px-4 py-2 bg-[var(--card)] border border-[var(--border)] rounded-xl text-center">
            <span class="text-xs text-text-secondary block">Total Dikumpulkan</span>
            <span class="text-lg font-bold text-text-primary">{{ $submissions->total() }}</span>
        </div>
        <div class="px-4 py-2 bg-[var(--card)] border border-[var(--border)] rounded-xl text-center">
            <span class="text-xs text-text-secondary block">Sudah Dinilai</span>
            <span class="text-lg font-bold text-green-600">{{ $tugas->submissions()->where('status', 'graded')->count() }}</span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center justify-between">
        <h3 class="font-bold text-text-primary flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-primary"></i> Daftar Pengumpulan Peserta
        </h3>
    </div>

    @if($submissions->isEmpty())
        <div class="text-center py-16">
            <i data-lucide="inbox" class="w-12 h-12 text-border mx-auto mb-3"></i>
            <h4 class="text-text-primary font-medium">Belum ada pengumpulan</h4>
            <p class="text-text-secondary text-sm mt-1">Peserta belum mengunggah file untuk penugasan ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary/40 text-text-secondary uppercase text-xs border-b border-border">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">File / Berkas</th>
                        @if($tugas->tipe === 'upload_sertifikat')
                            <th class="px-6 py-3">Detail Sertifikat</th>
                        @endif
                        <th class="px-6 py-3">Waktu Submit</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Nilai</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-secondary/20 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-text-primary">{{ $sub->user->nama ?? '-' }}</div>
                            <div class="text-xs text-text-secondary">NIP: {{ $sub->user->nip ?? '-' }} &bull; {{ $sub->user->unitKerja->nama ?? 'Umum' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($sub->file_path)
                                <div class="flex flex-col gap-1.5">
                                    <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-primary hover:underline font-medium text-xs bg-primary/5 px-2.5 py-1.5 rounded-lg border border-primary/20 w-fit">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        {{ Str::limit($sub->file_nama_asli ?? 'Berkas Tugas', 25) }}
                                    </a>
                                    <button type="button" onclick="openPreview('{{ asset('storage/' . $sub->file_path) }}', '{{ $sub->file_nama_asli ?? '' }}')" class="inline-flex items-center gap-1 text-primary hover:underline font-medium text-xs w-fit">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Preview
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-text-secondary italic">Tidak ada file</span>
                            @endif

                            @if($sub->catatan_peserta)
                                <p class="text-xs text-text-secondary mt-1 max-w-xs truncate" title="{{ $sub->catatan_peserta }}">
                                    <span class="font-medium">Catatan:</span> {{ $sub->catatan_peserta }}
                                </p>
                            @endif
                        </td>
                        @if($tugas->tipe === 'upload_sertifikat')
                        <td class="px-6 py-4 text-xs text-text-secondary">
                            <div><strong>No:</strong> {{ $sub->nomor_sertifikat ?? '-' }}</div>
                            <div><strong>Instansi:</strong> {{ $sub->penyelenggara ?? '-' }}</div>
                            <div><strong>Tgl:</strong> {{ $sub->tanggal_sertifikat ? $sub->tanggal_sertifikat->format('d M Y') : '-' }}</div>
                        </td>
                        @endif
                        <td class="px-6 py-4 text-xs text-text-secondary">
                            <div>{{ $sub->created_at->format('d M Y, H:i') }}</div>
                            @if($sub->is_late)
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-danger/10 text-danger">Terlambat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($sub->status === 'graded')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Sudah Dinilai</span>
                            @elseif($sub->status === 'need_revision')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Perlu Revisi</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Review</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($sub->nilai !== null)
                                <span class="text-base font-bold text-primary">{{ number_format($sub->nilai, 1) }}</span>
                            @else
                                <span class="text-xs text-text-secondary">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openGradingModal({{ $sub->id }}, '{{ addslashes($sub->user->nama) }}', '{{ $sub->nilai ?? '' }}', `{{ addslashes($sub->feedback_instruktur ?? '') }}`, '{{ $sub->status }}')" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white transition-colors">
                                <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Nilai
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-border">
            {{ $submissions->links() }}
        </div>
    @endif
</div>

<!-- Modal Grading -->
<div id="gradingModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-border mb-4">
            <h3 class="font-bold text-text-primary text-base">Penilaian Peserta</h3>
            <button onclick="closeGradingModal()" class="text-text-secondary hover:text-text-primary">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="gradingForm" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Peserta</label>
                    <p id="modalPesertaNama" class="text-sm font-bold text-text-primary">-</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="modalNilai" class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nilai (0 - 100) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" id="modalNilai" class="w-full px-3 py-2 rounded-lg border border-border bg-[var(--card)] text-text-primary text-lg font-bold focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>
                    </div>

                    <div>
                        <label for="modalStatus" class="block text-xs font-semibold text-text-secondary uppercase mb-1">Status Penilaian <span class="text-danger">*</span></label>
                        <select name="status" id="modalStatus" class="w-full px-3 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>
                            <option value="graded">Disetujui / Selesai Dinilai</option>
                            <option value="need_revision">Perlu Revisi (Koreksi)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="modalFeedback" class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan & Feedback untuk Peserta</label>
                    <textarea name="feedback_instruktur" id="modalFeedback" rows="3" placeholder="Berikan komentar, evaluasi kelebihan atau kekurangan hasil pengerjaan..." class="w-full px-3 py-2 rounded-lg border border-border bg-[var(--card)] text-text-primary text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none"></textarea>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeGradingModal()" class="px-4 py-2 rounded-lg text-sm text-text-secondary hover:bg-secondary">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-primary hover:bg-primary-hover text-white shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i> Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Preview File -->
<div id="previewModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl max-w-3xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-border">
            <h3 class="font-bold text-text-primary text-sm">Preview File</h3>
            <button type="button" onclick="closePreview()" class="text-text-secondary hover:text-text-primary">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4 bg-slate-100 dark:bg-slate-900">
            <img id="previewImage" class="hidden max-w-full mx-auto rounded-lg" alt="Preview gambar">
            <iframe id="previewPdf" class="hidden w-full h-[70vh] rounded-lg border-0" title="Preview PDF"></iframe>
            <div id="previewUnsupported" class="hidden text-center py-12">
                <i data-lucide="file-text" class="w-12 h-12 text-text-muted mx-auto mb-3"></i>
                <p class="text-sm text-text-primary font-medium">File tidak dapat dipreview langsung</p>
                <p class="text-xs text-text-secondary mt-1 mb-4">Unduh file untuk melihat isinya.</p>
                <a id="previewDownloadLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold">
                    <i data-lucide="download" class="w-4 h-4"></i> Unduh File
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openGradingModal(submissionId, pesertaNama, currentNilai, currentFeedback, currentStatus) {
    const form = document.getElementById('gradingForm');
    form.action = "{{ url('admin/tugas-submission') }}/" + submissionId + "/nilai";
    document.getElementById('modalPesertaNama').textContent = pesertaNama;
    document.getElementById('modalNilai').value = currentNilai;
    document.getElementById('modalFeedback').value = currentFeedback;
    document.getElementById('modalStatus').value = currentStatus === 'need_revision' ? 'need_revision' : 'graded';
    
    document.getElementById('gradingModal').classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
}

function closeGradingModal() {
    document.getElementById('gradingModal').classList.add('hidden');
}

function openPreview(url, filename) {
    const ext = (url.split('.').pop() || '').toLowerCase().split('?')[0];
    const imgTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    const img = document.getElementById('previewImage');
    const pdf = document.getElementById('previewPdf');
    const unsupported = document.getElementById('previewUnsupported');
    const dl = document.getElementById('previewDownloadLink');

    img.classList.add('hidden');
    pdf.classList.add('hidden');
    unsupported.classList.add('hidden');
    img.removeAttribute('src');
    pdf.removeAttribute('src');

    if (imgTypes.includes(ext)) {
        img.src = url;
        img.classList.remove('hidden');
    } else if (ext === 'pdf') {
        pdf.src = url;
        pdf.classList.remove('hidden');
    } else {
        dl.href = url;
        unsupported.classList.remove('hidden');
    }

    document.getElementById('previewModal').classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    const pdf = document.getElementById('previewPdf');
    pdf.removeAttribute('src');
}
</script>
@endsection
