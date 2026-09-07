@extends('layouts.app')
@section('title', 'Statistik Peserta')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Statistik Peserta</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Rekapitulasi progres pembelajaran dan evaluasi seluruh peserta aktif.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.statistik.export') }}" class="btn btn-secondary text-[var(--text-primary)] text-sm font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                <i data-lucide="download" class="w-4 h-4 text-emerald-500"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
        {{-- Filter / sort header --}}
        <div class="px-6 py-4 border-b border-[var(--border)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[var(--muted)]/30">
            <h3 class="text-sm font-bold text-[var(--text-primary)] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="trending-up" class="w-4 h-4 text-primary"></i>
                Rekap Nilai & Progres
            </h3>
            <div class="flex items-center gap-1.5 p-1 bg-[var(--muted)]/60 rounded-xl border border-[var(--border)] text-xs font-semibold">
                <a href="{{ route('admin.statistik.index', ['sort'=>'nama','direction'=>$sort==='nama'&&$direction==='asc'?'desc':'asc']) }}"
                   class="px-3 py-1.5 rounded-lg transition-all {{ $sort==='nama' ? 'bg-[var(--card)] text-primary shadow-xs' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    Nama {!! $sort==='nama' ? ($direction==='asc'?'↑':'↓') : '' !!}
                </a>
                <a href="{{ route('admin.statistik.index', ['sort'=>'modul','direction'=>$sort==='modul'&&$direction==='desc'?'asc':'desc']) }}"
                   class="px-3 py-1.5 rounded-lg transition-all {{ $sort==='modul' ? 'bg-[var(--card)] text-primary shadow-xs' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    Materi {!! $sort==='modul' ? ($direction==='asc'?'↑':'↓') : '' !!}
                </a>
                <a href="{{ route('admin.statistik.index', ['sort'=>'nilai','direction'=>$sort==='nilai'&&$direction==='desc'?'asc':'desc']) }}"
                   class="px-3 py-1.5 rounded-lg transition-all {{ $sort==='nilai' ? 'bg-[var(--card)] text-primary shadow-xs' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    Nilai {!! $sort==='nilai' ? ($direction==='asc'?'↑':'↓') : '' !!}
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="py-3.5 px-6 text-center w-14">No</th>
                        <th class="py-3.5 px-6">Nama Peserta</th>
                        <th class="py-3.5 px-6">Jabatan</th>
                        <th class="py-3.5 px-6 text-center">Materi Selesai</th>
                        <th class="py-3.5 px-6 text-center">Rata-rata Nilai</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @php $totalMateri = \App\Models\Materi::where('is_active', true)->count(); @endphp
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            <td class="py-4 px-6 text-xs text-[var(--text-secondary)] font-mono text-center">{{ $loop->iteration }}</td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-[var(--text-primary)]">{{ $user->nama }}</p>
                                <p class="text-xs text-[var(--text-secondary)] font-mono mt-0.5">{{ $user->nip }}</p>
                            </td>
                            <td class="py-4 px-6 text-xs text-[var(--text-secondary)]">{{ $user->jabatan->nama_jabatan ?? '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                @php $done = $user->materi_selesai; $all = $totalMateri; @endphp
                                <span class="font-semibold text-xs {{ $done === $all && $all > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-[var(--text-primary)]' }}">
                                    {{ $done }} <span class="text-[var(--text-secondary)]">/ {{ $all }}</span>
                                </span>
                                @if($all > 0)
                                    <div class="w-24 mx-auto bg-[var(--muted)] rounded-full h-1.5 mt-1.5 overflow-hidden">
                                        <div class="bg-primary h-1.5 rounded-full" style="width:{{ $all > 0 ? ($done/$all*100) : 0 }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($user->rata_nilai > 0)
                                    <span class="font-bold text-sm {{ $user->rata_nilai >= 70 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ number_format($user->rata_nilai, 1) }}
                                    </span>
                                @else
                                    <span class="text-[var(--text-muted)] font-mono">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($user->materi_selesai === $totalMateri && $totalMateri > 0 && $user->rata_nilai >= 70)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> Kompeten
                                    </span>
                                @elseif($user->materi_selesai > 0 || $user->rata_nilai > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Berlangsung
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">
                                        Belum Mulai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-[var(--text-secondary)]">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]"></i>
                                <p class="font-medium">Belum Ada Peserta</p>
                                <p class="text-xs text-[var(--text-muted)] mt-1">Data peserta yang telah disetujui akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
