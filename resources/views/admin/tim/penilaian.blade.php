@extends('layouts.app')

@section('title', 'Rekap Penilaian Tim Pembinaan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.tim.index') }}" class="text-text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">Tim Pembinaan</span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Rekap Penilaian Tim Pembinaan</h1>
        <p class="text-xs text-text-secondary mt-1">Rata-rata nilai dari masing-masing Tim untuk setiap peserta.</p>
    </div>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-secondary/40 text-text-secondary uppercase text-xs border-b border-border">
                <tr>
                    <th class="px-4 py-3">Peserta</th>
                    @foreach($teams as $key => $label)
                        <th class="px-4 py-3 text-center">{{ str_replace('Tim Pembinaan ', '', $label) }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-center">Rata-rata Tim</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($pesertas as $p)
                @php
                    $valid = array_filter($nilaiTim[$p->id] ?? [], fn($v) => $v !== null);
                    $avg = !empty($valid) ? array_sum($valid) / count($valid) : null;
                @endphp
                <tr class="hover:bg-secondary/10 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-medium text-text-primary">{{ $p->nama }}</div>
                        <div class="text-xs text-text-secondary">NIP: {{ $p->nip }}</div>
                    </td>
                    @foreach($teams as $key => $label)
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ ($nilaiTim[$p->id][$key] ?? null) !== null ? 'text-text-primary' : 'text-text-muted' }}">
                                {{ ($nilaiTim[$p->id][$key] ?? null) !== null ? number_format($nilaiTim[$p->id][$key], 1) : '-' }}
                            </span>
                        </td>
                    @endforeach
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold {{ $avg !== null ? 'text-primary' : 'text-text-muted' }}">
                            {{ $avg !== null ? number_format($avg, 1) : '-' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($teams) + 2 }}" class="px-4 py-12 text-center text-text-muted text-sm">Belum ada peserta.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
