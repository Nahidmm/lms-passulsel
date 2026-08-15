@extends('layouts.app')

@section('title', 'Manajemen Bank Soal')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-display font-bold text-primary">Bank Soal Evaluasi</h1>
        <p class="text-text-secondary mt-1">Kelola pertanyaan dan pilihan jawaban untuk setiap jabatan.</p>
    </div>
    <a href="{{ route('admin.soal.create') }}" class="bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Soal
    </a>
</div>

<div class="space-y-8">
    @foreach($jabatans as $jabatan)
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="p-4 bg-secondary/50 border-b border-border flex justify-between items-center">
                <h3 class="font-display font-bold text-text-primary text-lg flex items-center gap-2">
                    <i data-lucide="briefcase" class="w-5 h-5 text-accent"></i> {{ $jabatan->nama_jabatan }}
                </h3>
                <span class="text-xs bg-white border border-border px-2 py-1 rounded font-medium text-text-secondary">{{ $jabatan->soals->count() }} Soal</span>
            </div>
            
            <div class="p-0">
                @if($jabatan->soals->isEmpty())
                    <p class="text-center text-text-secondary py-6 text-sm">Belum ada soal untuk jabatan ini.</p>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-text-secondary uppercase bg-white border-b border-border">
                            <tr>
                                <th class="px-6 py-3 w-16">ID</th>
                                <th class="px-6 py-3">Pertanyaan</th>
                                <th class="px-6 py-3 w-40 text-center">Status</th>
                                <th class="px-6 py-3 w-32 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jabatan->soals as $soal)
                                <tr class="border-b border-border last:border-0 hover:bg-secondary/30 transition-colors">
                                    <td class="px-6 py-4 text-text-secondary">#{{ $soal->id }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-text-primary line-clamp-2">{{ $soal->pertanyaan }}</p>
                                        <div class="mt-2 flex gap-2">
                                            @foreach($soal->pilihanJawaban as $pilihan)
                                                <span class="text-xs px-2 py-0.5 rounded border {{ $pilihan->is_correct ? 'bg-success/10 border-success text-success font-bold' : 'bg-gray-100 border-border text-gray-500' }}">
                                                    {{ $pilihan->huruf }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($soal->is_active)
                                            <span class="bg-success/10 text-success text-xs font-bold px-2 py-1 rounded">Aktif</span>
                                        @else
                                            <span class="bg-danger/10 text-danger text-xs font-bold px-2 py-1 rounded">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.soal.edit', $soal->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2 py-1.5 rounded transition-colors" title="Edit">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2 py-1.5 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
</div>

@endsection
