@extends('layouts.app')

@section('title', 'Statistik Peserta')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Statistik Peserta</h1>
    <p class="text-text-secondary mt-1">Rekapitulasi progres pembelajaran dan nilai evaluasi seluruh peserta aktif.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
    <!-- Header/Filter Area -->
    <div class="p-4 border-b border-border bg-secondary/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
            <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i> Rekap Nilai Peserta
        </h3>

        <!-- Simple Sorting (Query Params) -->
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-text-secondary">Urutkan:</span>
            <div class="flex bg-white border border-border rounded-lg overflow-hidden text-sm">
                <a href="{{ route('admin.statistik.index', ['sort' => 'nama', 'direction' => $sort === 'nama' && $direction === 'asc' ? 'desc' : 'asc']) }}" 
                   class="px-3 py-1.5 {{ $sort === 'nama' ? 'bg-primary/10 text-primary font-bold' : 'text-text-secondary hover:bg-secondary' }} border-r border-border">
                   Nama {!! $sort === 'nama' ? ($direction === 'asc' ? '&uarr;' : '&darr;') : '' !!}
                </a>
                <a href="{{ route('admin.statistik.index', ['sort' => 'modul', 'direction' => $sort === 'modul' && $direction === 'desc' ? 'asc' : 'desc']) }}" 
                   class="px-3 py-1.5 {{ $sort === 'modul' ? 'bg-primary/10 text-primary font-bold' : 'text-text-secondary hover:bg-secondary' }} border-r border-border">
                   Modul Selesai {!! $sort === 'modul' ? ($direction === 'asc' ? '&uarr;' : '&darr;') : '' !!}
                </a>
                <a href="{{ route('admin.statistik.index', ['sort' => 'nilai', 'direction' => $sort === 'nilai' && $direction === 'desc' ? 'asc' : 'desc']) }}" 
                   class="px-3 py-1.5 {{ $sort === 'nilai' ? 'bg-primary/10 text-primary font-bold' : 'text-text-secondary hover:bg-secondary' }}">
                   Rata-rata Nilai {!! $sort === 'nilai' ? ($direction === 'asc' ? '&uarr;' : '&darr;') : '' !!}
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                <tr>
                    <th class="px-4 py-3 w-10">No</th>
                    <th class="px-4 py-3">Nama Peserta / NIP</th>
                    <th class="px-4 py-3">Jabatan</th>
                    <th class="px-4 py-3 text-center">Materi Selesai</th>
                    <th class="px-4 py-3 text-center">Rata-rata Nilai</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr class="border-b border-border hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3 text-center text-text-secondary">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-text-primary">{{ $user->nama }}</p>
                            <p class="text-xs text-text-secondary">{{ $user->nip }}</p>
                        </td>
                        <td class="px-4 py-3 text-text-primary">
                            {{ $user->jabatan->nama_jabatan ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $totalMateri = \App\Models\Materi::where('is_active', true)->count();
                            @endphp
                            <span class="inline-block px-2 py-1 rounded {{ $user->materi_selesai === $totalMateri && $totalMateri > 0 ? 'bg-success/10 text-success font-bold' : 'bg-gray-100 text-gray-700 font-medium' }}">
                                {{ $user->materi_selesai }} / {{ $totalMateri }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-lg font-bold {{ $user->rata_nilai >= 70 ? 'text-success' : ($user->rata_nilai > 0 ? 'text-warning' : 'text-text-secondary') }}">
                                {{ $user->rata_nilai > 0 ? number_format($user->rata_nilai, 1) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->materi_selesai === $totalMateri && $totalMateri > 0 && $user->rata_nilai >= 70)
                                <span class="bg-success text-white text-xs font-bold px-2 py-1 rounded-full">Kompeten</span>
                            @elseif($user->materi_selesai > 0 || $user->rata_nilai > 0)
                                <span class="bg-warning text-white text-xs font-bold px-2 py-1 rounded-full">In Progress</span>
                            @else
                                <span class="bg-gray-200 text-gray-500 text-xs font-bold px-2 py-1 rounded-full">Belum Mulai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-text-secondary">Belum ada data peserta yang disetujui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
