@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Import Data User</h2>
        <a href="{{ route('admin.akun.index') }}" class="bg-gray-500 hover:bg-gray-600 text-[var(--text-primary)] font-bold py-2 px-4 rounded">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <p class="text-gray-700 text-sm mb-2">
                Silakan unggah file Excel (.xlsx, .xls) atau CSV yang berisi data user. 
                Pastikan format kolom sesuai dengan template yang disediakan.
            </p>
            <p class="text-gray-700 text-sm font-bold mb-4">
                Catatan Penting:
                <ul class="list-disc list-inside ml-2 font-normal">
                    <li>NIP harus unik (tidak boleh duplikat di sistem).</li>
                    <li>Password default adalah <code>password123</code> dan user akan diminta mengubahnya saat login pertama kali.</li>
                    <li>Jabatan yang diisi harus sudah ada di Master Data Jabatan. Jika tidak ada, baris tersebut akan diabaikan.</li>
                </ul>
            </p>
            <a href="{{ route('admin.users.import.template') }}" class="inline-block bg-green-600 hover:bg-green-700 text-[var(--text-primary)] font-bold py-2 px-4 rounded mb-6">
                Unduh Template CSV
            </a>
        </div>

        <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel / CSV</label>
                <input type="file" name="file" id="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('file')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-[var(--text-primary)] font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
