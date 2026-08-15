@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.soal.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Tambah Soal Baru</h2>
    </div>

    @if($errors->any())
        <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.soal.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="jabatan_id" class="block text-sm font-medium text-text-primary mb-1">Pilih Jabatan <span class="text-danger">*</span></label>
                <select id="jabatan_id" name="jabatan_id" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="is_active" class="block text-sm font-medium text-text-primary mb-1">Status Aktif</label>
                <select id="is_active" name="is_active" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Draft (Sembunyikan)</option>
                </select>
            </div>
        </div>

        <div>
            <label for="pertanyaan" class="block text-sm font-medium text-text-primary mb-1">Pertanyaan <span class="text-danger">*</span></label>
            <textarea id="pertanyaan" name="pertanyaan" rows="3" required
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('pertanyaan') }}</textarea>
        </div>

        <div>
            <label for="pembahasan" class="block text-sm font-medium text-text-primary mb-1">Pembahasan / Penjelasan (Opsional)</label>
            <textarea id="pembahasan" name="pembahasan" rows="2"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('pembahasan') }}</textarea>
        </div>

        <!-- Jawaban Section -->
        <div class="mt-8 border-t border-border pt-6">
            <h3 class="text-lg font-bold text-text-primary mb-4 flex items-center gap-2">
                <i data-lucide="list-checks" class="w-5 h-5 text-primary"></i> Pilihan Jawaban
            </h3>
            <p class="text-xs text-text-secondary mb-4">Pilih salah satu *radio button* untuk menentukan jawaban yang benar.</p>
            
            <div class="space-y-4">
                @php
                    $hurufs = ['A', 'B', 'C', 'D'];
                @endphp
                @foreach($hurufs as $index => $huruf)
                    <div class="flex items-start gap-3">
                        <div class="pt-2">
                            <input type="radio" name="jawaban_benar" value="{{ $index }}" required {{ old('jawaban_benar') == $index ? 'checked' : '' }}
                                class="w-4 h-4 text-primary focus:ring-primary border-border cursor-pointer" title="Jadikan jawaban benar">
                        </div>
                        <div class="w-10 h-10 shrink-0 bg-secondary rounded-lg flex items-center justify-center font-bold text-text-secondary border border-border">
                            {{ $huruf }}
                        </div>
                        <div class="flex-1">
                            <input type="text" name="pilihan[{{ $index }}]" value="{{ old('pilihan.'.$index) }}" required placeholder="Teks pilihan jawaban {{ $huruf }}"
                                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-border gap-3 mt-8">
            <a href="{{ route('admin.soal.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Simpan Soal
            </button>
        </div>
    </form>
</div>

@endsection
