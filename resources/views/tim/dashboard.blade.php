@extends('layouts.app')

@section('title', 'Dashboard ' . $tim->nama)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">{{ \App\Models\PenilaianTim::teamLabel($tim->tim) }}</h1>
        <p class="text-xs text-text-secondary mt-1">
            Koordinator: {{ $tim->nama }} (NIP: {{ $tim->nip }})
            @if($tim->anggota_tim) &bull; Anggota: {{ $tim->anggota_tim }} @endif
        </p>
    </div>

    <form method="GET" action="{{ route('tim.dashboard') }}" class="flex items-center gap-2">
        <label class="text-xs font-semibold text-text-secondary">Pekan:</label>
        <select name="pekan" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-border bg-[var(--card)] text-sm text-text-primary">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $pekan === $i ? 'selected' : '' }}>Pekan {{ $i }}</option>
            @endfor
        </select>
    </form>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('tim.penilaian.store') }}">
    @csrf
    <input type="hidden" name="pekan" value="{{ $pekan }}">

    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary/40 text-text-secondary uppercase text-xs border-b border-border">
                    <tr>
                        <th class="px-4 py-3">Peserta</th>
                        <th class="px-4 py-3 text-center">Nilai Pretest</th>
                        <th class="px-4 py-3 text-center">Nilai Pelatihan</th>
                        <th class="px-4 py-3 text-center" style="min-width:120px">Nilai Pekan (0-100)</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($pesertas as $p)
                    <tr class="hover:bg-secondary/10 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-text-primary">{{ $p->nama }}</div>
                            <div class="text-xs text-text-secondary">NIP: {{ $p->nip }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $p->nilai_pretest !== null ? ($p->nilai_pretest >= 70 ? 'text-emerald-600' : 'text-rose-600') : 'text-text-muted' }}">
                                {{ $p->nilai_pretest !== null ? number_format($p->nilai_pretest, 1) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $p->nilai_pelatihan !== null ? 'text-text-primary' : 'text-text-muted' }}">
                                {{ $p->nilai_pelatihan !== null ? number_format($p->nilai_pelatihan, 1) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" step="0.1" min="0" max="100" name="nilai[{{ $p->id }}]"
                                value="{{ $penilaian->has($p->id) ? $penilaian->get($p->id)->nilai : '' }}"
                                class="w-24 px-2 py-1.5 text-center rounded-lg border border-border bg-[var(--input)] text-text-primary text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="catatan[{{ $p->id }}]" maxlength="500"
                                value="{{ $penilaian->has($p->id) ? $penilaian->get($p->id)->catatan : '' }}"
                                placeholder="Catatan singkat..."
                                class="w-full px-3 py-1.5 rounded-lg border border-border bg-[var(--input)] text-text-primary text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-text-muted text-sm">Belum ada peserta terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border flex justify-end">
            <button type="submit" class="btn btn-primary text-white text-sm px-5 py-2.5 rounded-xl font-semibold">Simpan Penilaian Pekan {{ $pekan }}</button>
        </div>
    </div>
</form>
@endsection
