@extends('layouts.app')

@section('title', 'Penilaian Peserta')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Penilaian Peserta</h1>
        <p class="text-text-secondary text-sm mt-1">Pilih peserta untuk melihat riwayat kuis dan memberikan penilaian.</p>
    </div>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Peserta</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Jabatan</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Kuis Dikerjakan</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Rata-rata Nilai</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Total Poin</th>
                    <th class="py-4 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($users as $user)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-9 h-9 rounded-full object-cover border border-border shrink-0">
                            <div>
                                <p class="text-sm font-semibold text-text-primary">{{ $user->nama }}</p>
                                <p class="text-xs text-text-secondary">{{ $user->nip }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-text-primary">
                        {{ $user->jabatan->nama_jabatan ?? '-' }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($user->total_kuis_dikerjakan > 0)
                            <span class="px-2.5 py-1 bg-primary/10 text-primary rounded-lg text-xs font-bold">
                                {{ $user->total_kuis_dikerjakan }} Kuis
                            </span>
                        @else
                            <span class="text-xs text-text-secondary">Belum ada</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-lg font-bold {{ $user->rata_nilai >= 70 ? 'text-success' : ($user->rata_nilai > 0 ? 'text-warning' : 'text-text-secondary') }}">
                            {{ $user->rata_nilai > 0 ? number_format($user->rata_nilai, 1) : '-' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="px-2.5 py-1 bg-accent/10 text-accent rounded-lg text-xs font-bold">
                            {{ number_format($user->total_poin) }} Poin
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.penilaian.show', $user->id) }}"
                           class="btn btn-primary text-white text-xs font-medium px-3.5 py-1.5 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                            Detail Nilai
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-text-secondary">
                        <div class="w-14 h-14 bg-secondary rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="users" class="w-7 h-7 opacity-40"></i>
                        </div>
                        <p class="text-sm font-medium">Tidak ada peserta ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

