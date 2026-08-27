<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserImportController extends Controller
{
    public function index()
    {
        return view('admin.users.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('admin.akun.index')->with('success', 'Data User berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        // Format Header: NIP, NAMA LENGKAP, UNIT KERJA, PANGKAT/GOLONGAN, JABATAN, ALAMAT, NO HP, EMAIL
        $content = "NIP,NAMA LENGKAP,UNIT KERJA,PANGKAT/GOLONGAN,JABATAN,ALAMAT,NO HP,EMAIL\n";
        $content .= "198001012005011001,Budi Santoso,Dinas Pendidikan,III/c,Kepala Bidang,Jl. Contoh No 1,081234567890,budi@example.com\n";

        return response($content, 200, $headers);
    }
}
