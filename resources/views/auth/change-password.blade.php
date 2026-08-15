@extends('layouts.auth')

@section('title', 'Ubah Password Wajib')

@section('content')

<div class="text-center mb-6">
    <div class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    <h2 class="text-xl font-bold text-text-primary mb-1">Ganti Password</h2>
    <p class="text-sm text-text-secondary">Demi keamanan, Anda diwajibkan mengganti password bawaan sebelum melanjutkan.</p>
</div>

@if($errors->any())
    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('auth.change-password.post') }}" class="space-y-4">
    @csrf
    
    <div>
        <label for="password" class="block text-sm font-medium text-text-primary mb-1">Password Baru</label>
        <input type="password" id="password" name="password" required minlength="8" autofocus
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Min. 8 karakter">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1">Ulangi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Ketik ulang password">
    </div>

    <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-lg transition-colors mt-2">
        Simpan & Lanjutkan
    </button>
</form>

<div class="mt-6 text-center">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm text-text-secondary hover:text-danger font-medium transition-colors">
            Keluar
        </button>
    </form>
</div>

@endsection
