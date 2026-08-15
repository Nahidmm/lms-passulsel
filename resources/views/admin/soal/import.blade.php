@extends('layouts.app')

@section('title', 'Import Soal – ' . $materi->judul)

@section('content')

<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('admin.materi.edit', $materi->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Konfigurasi Kuis
    </a>
</div>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start gap-4 mb-2">
        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="upload-cloud" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Import Soal dari CSV</h1>
            <p class="text-text-secondary mt-1">Kuis: <span class="font-bold text-text-primary">{{ $materi->judul }}</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-success/10 border border-success/20 text-success px-5 py-4 rounded-xl flex gap-3 items-start text-sm">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-danger/10 border border-danger/20 text-danger px-5 py-4 rounded-xl flex gap-3 items-start text-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-danger/10 border border-danger/20 text-danger px-5 py-4 rounded-xl text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Download Template --}}
    <div class="bg-white rounded-xl border border-border shadow-sm p-6">
        <h3 class="text-lg font-bold text-text-primary mb-3 flex items-center gap-2">
            <i data-lucide="file-down" class="w-5 h-5 text-accent"></i> Langkah 1 – Download Template CSV
        </h3>
        <p class="text-sm text-text-secondary mb-4 leading-relaxed">
            Download template berikut, isi dengan soal-soal Anda, lalu upload kembali. Jangan mengubah nama kolom header.
        </p>
        <a href="{{ route('admin.soal.template', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-accent/10 hover:bg-accent text-accent hover:text-white font-bold px-5 py-2.5 rounded-xl transition-all border border-accent/30">
            <i data-lucide="download" class="w-4 h-4"></i> Download Template (CSV)
        </a>
    </div>

    {{-- Format Penjelasan --}}
    <div class="bg-white rounded-xl border border-border shadow-sm p-6">
        <h3 class="text-lg font-bold text-text-primary mb-4 flex items-center gap-2">
            <i data-lucide="info" class="w-5 h-5 text-primary"></i> Langkah 2 – Panduan Format CSV
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-secondary/50">
                        <th class="text-left px-3 py-2 border border-border font-bold text-text-primary rounded-tl">Tipe Soal</th>
                        <th class="text-left px-3 py-2 border border-border font-bold text-text-primary">tipe_soal</th>
                        <th class="text-left px-3 py-2 border border-border font-bold text-text-primary">Kolom yang diisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr class="hover:bg-secondary/20">
                        <td class="px-3 py-2 border border-border font-medium">Pilihan Ganda</td>
                        <td class="px-3 py-2 border border-border font-mono text-blue-600">pilihan_ganda</td>
                        <td class="px-3 py-2 border border-border text-text-secondary">pertanyaan, bobot, opsi_a–opsi_d, <strong>kunci_jawaban</strong> (isi A/B/C/D)</td>
                    </tr>
                    <tr class="hover:bg-secondary/20">
                        <td class="px-3 py-2 border border-border font-medium">Multi Select</td>
                        <td class="px-3 py-2 border border-border font-mono text-violet-600">multi_select</td>
                        <td class="px-3 py-2 border border-border text-text-secondary">pertanyaan, bobot, opsi_a–opsi_d, <strong>jawaban_benar_multi</strong> (misal: A,C,D)</td>
                    </tr>
                    <tr class="hover:bg-secondary/20">
                        <td class="px-3 py-2 border border-border font-medium">Essay</td>
                        <td class="px-3 py-2 border border-border font-mono text-green-600">essay</td>
                        <td class="px-3 py-2 border border-border text-text-secondary">pertanyaan, bobot, <strong>jawaban_free_text</strong> (contoh jawaban / kunci, opsional)</td>
                    </tr>
                    <tr class="hover:bg-secondary/20">
                        <td class="px-3 py-2 border border-border font-medium">Isian Singkat</td>
                        <td class="px-3 py-2 border border-border font-mono text-yellow-600">isian_singkat</td>
                        <td class="px-3 py-2 border border-border text-text-secondary">pertanyaan, bobot, <strong>jawaban_free_text</strong> (kunci jawaban isian)</td>
                    </tr>
                    <tr class="hover:bg-secondary/20">
                        <td class="px-3 py-2 border border-border font-medium">Menjodohkan</td>
                        <td class="px-3 py-2 border border-border font-mono text-orange-600">menjodohkan</td>
                        <td class="px-3 py-2 border border-border text-text-secondary"><strong>pasangan_kiri</strong> & <strong>pasangan_kanan</strong> dipisah dengan <code class="bg-secondary px-1 rounded">|</code> (misal: Indonesia|Malaysia)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="bg-white rounded-xl border border-border shadow-sm p-6">
        <h3 class="text-lg font-bold text-text-primary mb-5 flex items-center gap-2">
            <i data-lucide="upload" class="w-5 h-5 text-success"></i> Langkah 3 – Upload File CSV
        </h3>
        <form action="{{ route('admin.soal.import.store', $materi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Pilih File CSV <span class="text-danger">*</span></label>
                <div id="drop-zone" class="border-2 border-dashed border-border rounded-xl p-8 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all">
                    <i data-lucide="file-spreadsheet" class="w-12 h-12 text-border mx-auto mb-3"></i>
                    <p class="font-medium text-text-primary" id="drop-text">Klik atau drag & drop file CSV di sini</p>
                    <p class="text-xs text-text-secondary mt-1">Format: .csv | Maks: 2MB</p>
                    <input type="file" id="file_csv" name="file_csv" accept=".csv,.txt" class="hidden" onchange="updateDropText(this)">
                </div>
                <p class="text-xs text-text-secondary mt-1.5">Pastikan file menggunakan encoding UTF-8.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-text-primary mb-3">Mode Import <span class="text-danger">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-primary hover:shadow-sm transition-all">
                        <input type="radio" name="mode_import" value="tambah" checked class="mt-1 w-5 h-5 text-primary border-border focus:ring-primary">
                        <div>
                            <p class="text-sm font-bold text-text-primary group-hover:text-primary transition-colors">Tambahkan ke Soal Existing</p>
                            <p class="text-xs text-text-secondary mt-1 leading-relaxed">Soal dari CSV akan ditambahkan ke soal yang sudah ada.</p>
                        </div>
                    </label>
                    <label class="group flex items-start gap-4 p-4 bg-white border border-danger/30 rounded-xl cursor-pointer hover:border-danger hover:shadow-sm transition-all">
                        <input type="radio" name="mode_import" value="ganti" class="mt-1 w-5 h-5 text-danger border-border focus:ring-danger">
                        <div>
                            <p class="text-sm font-bold text-danger">Ganti Semua Soal</p>
                            <p class="text-xs text-text-secondary mt-1 leading-relaxed">Semua soal lama akan <strong class="text-danger">dihapus</strong> dan diganti dengan soal dari CSV.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-border">
                <button type="submit" id="submit-btn" class="bg-primary hover:bg-primary-hover text-white font-bold py-3 px-8 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i> Import Soal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file_csv');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary', 'bg-primary/5');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary', 'bg-primary/5');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary', 'bg-primary/5');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateDropText(fileInput);
        }
    });

    function updateDropText(input) {
        const text = document.getElementById('drop-text');
        if (input.files && input.files[0]) {
            text.textContent = '✓ ' + input.files[0].name;
            text.classList.add('text-success');
            dropZone.classList.add('border-success', 'bg-success/5');
        }
    }
</script>
@endpush
