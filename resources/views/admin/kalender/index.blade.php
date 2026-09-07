@extends('layouts.app')

@section('title', 'Manajemen Kalender Akademik')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-sans font-bold text-primary">Kalender Akademik</h1>
        <p class="text-text-secondary mt-1">Kelola hari libur dan jadwal evaluasi dalam kalender sistem.</p>
    </div>
    <a href="{{ route('admin.kalender.create') }}" class="btn btn-primary text-xs flex items-center gap-2">
        <i data-lucide="calendar-plus" class="w-4 h-4"></i> Tambah Agenda
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- List Events -->
    <div class="lg:col-span-3 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                    <tr>
                        <th class="px-6 py-3 w-16 text-center">No</th>
                        <th class="px-6 py-3">Nama Agenda / Kegiatan</th>
                        <th class="px-6 py-3 w-40">Tanggal</th>
                        <th class="px-6 py-3 w-40 text-center">Jenis</th>
                        <th class="px-6 py-3 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr class="border-b border-border hover:bg-secondary/30 transition-colors">
                            <td class="px-6 py-4 text-center text-text-secondary">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-text-primary">{{ $event->title }}</p>
                                <p class="text-xs text-text-secondary line-clamp-1 mt-0.5">{{ $event->description }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-text-primary">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                                @if($event->end_date && $event->end_date != $event->start_date)
                                    - {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($event->type === 'libur')
                                    <span class="bg-danger/10 text-danger text-xs font-bold px-2 py-1 rounded">Libur Nasional</span>
                                @elseif($event->type === 'evaluasi')
                                    <span class="bg-warning/10 text-warning text-xs font-bold px-2 py-1 rounded">Jadwal Evaluasi</span>
                                @else
                                    <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded">Info Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.kalender.edit', $event->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2 py-1.5 rounded transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.kalender.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2 py-1.5 rounded transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-text-secondary">Belum ada agenda kalender terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

