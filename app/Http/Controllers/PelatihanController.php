<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    // ==========================================
    // ADMIN ROUTES
    // ==========================================

    public function index()
    {
        $pelatihans = Pelatihan::latest()->paginate(10);
        return view('admin.pelatihan.index', compact('pelatihans'));
    }

    public function create()
    {
        return view('admin.pelatihan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pelatihan = Pelatihan::create($validated);

        if ($pelatihan->is_active) {
            $pesertaIds = User::where('role', 'peserta')->where('status_akun', 'approved')->pluck('id')->toArray();
            if (!empty($pesertaIds)) {
                Notification::kirim(
                    $pesertaIds,
                    'Pelatihan Baru Tersedia',
                    "Pelatihan baru \"{$pelatihan->judul}\" telah ditambahkan. Segera cek dan daftar!",
                    'info',
                    'book-open',
                    route('peserta.pelatihan.index')
                );
            }
        }

        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    public function show(Pelatihan $pelatihan)
    {
        $pelatihan->load(['materis' => function($q) {
            $q->orderBy('urutan')->with('soals');
        }]);
        return view('admin.pelatihan.builder', compact('pelatihan'));
    }

    public function edit(Pelatihan $pelatihan)
    {
        return view('admin.pelatihan.edit', compact('pelatihan'));
    }

    public function update(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pelatihan->update($validated);
        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil diperbarui.');
    }

    public function destroy(Pelatihan $pelatihan)
    {
        $pelatihan->delete();
        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil dihapus.');
    }

    public function reorder(Request $request, Pelatihan $pelatihan)
    {
        return response()->json(['success' => true]);
    }

    // ==========================================
    // PESERTA ROUTES
    // ==========================================

    public function indexPeserta(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Pelatihan::where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $pelatihans = $query->latest()->get(); // Use get() instead of paginate for simpler filtering logic on client side if needed, or keep paginate
        
        $activePelatihan = auth()->user()->getActivePelatihan();

        // If status filter is applied, we filter the collection manually since status depends on pivot table
        if ($status) {
            $pelatihans = $pelatihans->filter(function ($pelatihan) use ($status, $activePelatihan) {
                $userId = auth()->user()->id;
                $progres = \App\Models\ProgresPelatihan::where('user_id', $userId)
                    ->where('pelatihan_id', $pelatihan->id)
                    ->first();
                
                $currentStatus = $progres ? $progres->status : 'belum';
                
                // Active pelatihan might be marked as 'aktif' but another course is locked
                if ($currentStatus === 'belum' && $activePelatihan && $activePelatihan->pelatihan_id !== $pelatihan->id) {
                    $currentStatus = 'terkunci';
                }

                return $currentStatus === $status;
            });
        }

        // Paginate manually since we filtered a collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $pelatihans = new \Illuminate\Pagination\LengthAwarePaginator(
            $pelatihans->forPage($page, $perPage),
            $pelatihans->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('peserta.pelatihan.index', compact('pelatihans', 'activePelatihan', 'search', 'status'));
    }

    public function enrollPeserta(Request $request, Pelatihan $pelatihan)
    {
        if (!$pelatihan->is_active) {
            abort(403);
        }

        $user = auth()->user();

        // Check if user already has an active pelatihan
        $active = $user->getActivePelatihan();
        if ($active && $active->pelatihan_id !== $pelatihan->id) {
            return back()->with('error', 'Anda harus menyelesaikan pelatihan yang sedang aktif terlebih dahulu.');
        }

        // Check if already enrolled or completed
        $progres = \App\Models\ProgresPelatihan::where('user_id', $user->id)
            ->where('pelatihan_id', $pelatihan->id)
            ->first();

        if (!$progres) {
            \App\Models\ProgresPelatihan::create([
                'user_id' => $user->id,
                'pelatihan_id' => $pelatihan->id,
                'status' => 'aktif'
            ]);
        } elseif ($progres->status === 'selesai') {
            return redirect()->route('peserta.pelatihan.show', $pelatihan->id)->with('info', 'Anda sudah menyelesaikan pelatihan ini.');
        }

        return redirect()->route('peserta.pelatihan.show', $pelatihan->id)->with('success', 'Berhasil memulai pelatihan.');
    }

    public function showPeserta(Pelatihan $pelatihan)
    {
        if (!$pelatihan->is_active) {
            abort(403);
        }
        
        $user = auth()->user();

        // Check enrollment status for THIS course
        $progresPelatihan = \App\Models\ProgresPelatihan::where('user_id', $user->id)
            ->where('pelatihan_id', $pelatihan->id)
            ->first();

        // Lock check: if user has an active pelatihan and it's NOT this one, 
        // they can only access this course if they have ALREADY finished it.
        $active = $user->getActivePelatihan();
        if ($active && $active->pelatihan_id !== $pelatihan->id) {
            if (!$progresPelatihan || $progresPelatihan->status !== 'selesai') {
                return redirect()->route('peserta.pelatihan.index')->with('error', 'Akses dikunci. Harap selesaikan pelatihan aktif Anda terlebih dahulu.');
            }
        }

        // If user is not enrolled yet, auto-enroll them now
        if (!$progresPelatihan) {
            $progresPelatihan = \App\Models\ProgresPelatihan::create([
                'user_id'     => $user->id,
                'pelatihan_id' => $pelatihan->id,
                'status'      => 'aktif',
            ]);
        }

        $materis = $pelatihan->materis()->where('is_active', true)->orderBy('urutan')->get();

        // Enforce sequential learning: Materi is locked if previous materi is not completed
        $materiStatus = [];
        $previousCompleted = true; // The first materi is always unlocked by default
        
        $completedCount = 0;
        
        foreach ($materis as $materi) {
            $mProgres = \App\Models\ProgresMateri::firstOrCreate(
                ['user_id' => $user->id, 'materi_id' => $materi->id],
                ['status' => 'belum']
            );
            
            $isLocked = !$previousCompleted;
            
            // Still check explicit manual prerequisite if any
            if ($materi->prasyarat_materi_id) {
                $prasyaratProgres = \App\Models\ProgresMateri::where('user_id', $user->id)
                    ->where('materi_id', $materi->prasyarat_materi_id)
                    ->first();
                if (!$prasyaratProgres || $prasyaratProgres->status !== 'selesai') {
                    $isLocked = true;
                }
            }

            $materiStatus[$materi->id] = [
                'materi' => $materi,
                'progres' => $mProgres,
                'status' => $mProgres->status,
                'is_locked' => $isLocked
            ];
            
            if ($mProgres->status === 'selesai') {
                $completedCount++;
            } else {
                $previousCompleted = false;
            }
        }
        
        $totalMateris = $materis->count();
        $persenProgress = $totalMateris > 0 ? floor(($completedCount / $totalMateris) * 100) : 0;

        // Fetch certificate if pelatihan is completed
        $sertifikat = null;
        if ($progresPelatihan && $progresPelatihan->status === 'selesai') {
            $sertifikat = \App\Models\Sertifikat::where('user_id', $user->id)
                ->where('pelatihan_id', $pelatihan->id)
                ->first();
        }

        return view('peserta.pelatihan.show', compact('pelatihan', 'materis', 'materiStatus', 'persenProgress', 'progresPelatihan', 'sertifikat'));
    }
}
