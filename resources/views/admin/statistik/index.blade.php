@extends('layouts.app')
@section('title', 'Statistik Peserta')

@section('content')
<div class="space-y-5 py-1">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">/</span>
        <span class="current">Statistik Peserta</span>
    </div>

    <div>
        <h1 class="text-2xl font-black text-[var(--text-primary)]">Statistik Peserta</h1>
        <p class="text-[var(--text-secondary)] text-sm mt-1">Rekapitulasi progres pembelajaran dan nilai evaluasi seluruh peserta aktif.</p>
    </div>

    <div class="game-card overflow-hidden">
        {{-- Filter / sort header --}}
        <div class="card-header">
            <h3>
                <i data-lucide="trending-up" class="w-4 h-4 text-violet-400"></i>
                Rekap Nilai Peserta
            </h3>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('admin.statistik.export') }}" class="btn btn-success text-xs px-3 py-2 min-h-0 h-8">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Export CSV
                </a>
                <div class="sort-tabs">
                    <a href="{{ route('admin.statistik.index', ['sort'=>'nama','direction'=>$sort==='nama'&&$direction==='asc'?'desc':'asc']) }}"
                       class="sort-tab {{ $sort==='nama' ? 'active' : '' }}">
                        Nama {!! $sort==='nama' ? ($direction==='asc'?'â†‘':'â†“') : '' !!}
                    </a>
                    <a href="{{ route('admin.statistik.index', ['sort'=>'modul','direction'=>$sort==='modul'&&$direction==='desc'?'asc':'desc']) }}"
                       class="sort-tab {{ $sort==='modul' ? 'active' : '' }}">
                        Materi {!! $sort==='modul' ? ($direction==='asc'?'â†‘':'â†“') : '' !!}
                    </a>
                    <a href="{{ route('admin.statistik.index', ['sort'=>'nilai','direction'=>$sort==='nilai'&&$direction==='desc'?'asc':'desc']) }}"
                       class="sort-tab {{ $sort==='nilai' ? 'active' : '' }}">
                        Nilai {!! $sort==='nilai' ? ($direction==='asc'?'â†‘':'â†“') : '' !!}
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/7 bg-[var(--card)] border border-[var(--border)] shadow-sm">
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)] w-10">No</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Nama Peserta</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Jabatan</th>
                        <th class="py-3.5 px-5 text-center text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Materi Selesai</th>
                        <th class="py-3.5 px-5 text-center text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Rata-rata Nilai</th>
                        <th class="py-3.5 px-5 text-center text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @php $totalMateri = \App\Models\Materi::where('is_active', true)->count(); @endphp
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors">
                            <td class="py-4 px-5 text-sm text-[var(--text-muted)] text-center">{{ $loop->iteration }}</td>
                            <td class="py-4 px-5">
                                <p class="font-bold text-[var(--text-primary)] text-sm">{{ $user->nama }}</p>
                                <p class="text-xs text-[var(--text-muted)] font-mono mt-0.5">{{ $user->nip }}</p>
                            </td>
                            <td class="py-4 px-5 text-sm text-[var(--text-primary)]">{{ $user->jabatan->nama_jabatan ?? '-' }}</td>
                            <td class="py-4 px-5 text-center">
                                @php $done = $user->materi_selesai; $all = $totalMateri; @endphp
                                <span class="text-sm font-black {{ $done === $all && $all > 0 ? 'text-emerald-400' : 'text-[var(--text-primary)]' }}">
                                    {{ $done }} <span class="text-[var(--text-muted)] font-semibold">/ {{ $all }}</span>
                                </span>
                                @if($all > 0)
                                    <div class="progress-track mt-1.5 w-20 mx-auto h-1">
                                        <div class="progress-bar-violet h-1" style="width:{{ $all > 0 ? ($done/$all*100) : 0 }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($user->rata_nilai > 0)
                                    <span class="text-lg font-black {{ $user->rata_nilai >= 70 ? 'text-emerald-400' : 'text-amber-400' }}">
                                        {{ number_format($user->rata_nilai, 1) }}
                                    </span>
                                @else
                                    <span class="text-slate-600 font-bold">â€”</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($user->materi_selesai === $totalMateri && $totalMateri > 0 && $user->rata_nilai >= 70)
                                    <span class="badge badge-emerald text-[10px]"><i data-lucide="shield-check" class="w-2.5 h-2.5"></i> Kompeten</span>
                                @elseif($user->materi_selesai > 0 || $user->rata_nilai > 0)
                                    <span class="badge badge-amber text-[10px]"><i data-lucide="loader" class="w-2.5 h-2.5"></i> Berlangsung</span>
                                @else
                                    <span class="badge badge-rose text-[10px]"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Belum Mulai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i data-lucide="users" class="w-6 h-6"></i>
                                    </div>
                                    <h4>Belum Ada Peserta</h4>
                                    <p>Data peserta yang sudah disetujui akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

