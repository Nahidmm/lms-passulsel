@extends('layouts.auth')

@section('title', 'Login')

@section('content')

@if($errors->any())
    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('login.post') }}" class="space-y-5">
    @csrf
    
    <div>
        <label for="nip" class="block text-sm font-medium text-text-primary mb-1">NIP Pegawai</label>
        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Masukkan NIP Anda">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-text-primary mb-1">Password</label>
        <input type="password" id="password" name="password" required
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Masukkan password">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <span class="text-sm text-text-secondary">Ingat saya</span>
        </label>
    </div>

    <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-4 rounded-lg transition-colors">
        Masuk
    </button>
</form>

<div class="mt-8 pt-6 border-t border-border text-center text-sm text-text-secondary">
    Belum memiliki akun? <br>
    <a href="{{ route('register') }}" class="text-accent hover:text-accent-hover font-semibold mt-1 inline-block">Daftar Akun Baru</a>
</div>

@endsection

