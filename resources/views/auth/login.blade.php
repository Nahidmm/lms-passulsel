@extends('layouts.auth')

@section('title', 'Login - STRAPSUSPAS')

@section('content')

@if($errors->any())
    <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-6 text-xs">
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-6 text-xs">
        {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 px-4 py-3 rounded-xl mb-6 text-xs">
        {{ session('warning') }}
    </div>
@endif

<form method="POST" action="{{ route('login.post') }}" class="space-y-4">
    @csrf
    
    <div>
        <label for="nip" class="block text-xs font-semibold text-white/80 mb-1.5 uppercase tracking-wider">NIP / Email Pegawai</label>
        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
            class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm focus:border-sky-400 focus:ring-1 focus:ring-sky-400 outline-none transition-all placeholder:text-white/30"
            placeholder="Masukkan NIP atau Email">
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold text-white/80 mb-1.5 uppercase tracking-wider">Password</label>
        <input type="password" id="password" name="password" required
            class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm focus:border-sky-400 focus:ring-1 focus:ring-sky-400 outline-none transition-all placeholder:text-white/30"
            placeholder="Masukkan password">
    </div>

    <div class="flex items-center justify-between text-xs text-white/60">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white/5 border-white/20 text-sky-500 focus:ring-0">
            <span>Ingat sesi saya</span>
        </label>
    </div>

    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold py-3 px-4 rounded-full text-sm transition-all shadow-lg shadow-sky-500/20 hover:scale-[1.01] active:scale-[0.99] mt-2">
        Masuk ke Portal
    </button>
</form>

@if(!app()->environment('production'))
<!-- Demo Account Quick Fill -->
<div class="mt-6 pt-5 border-t border-white/10">
    <p class="text-[11px] font-semibold text-white/50 text-center uppercase tracking-wider mb-2.5">
        Akun Demo / Cepat (Klik untuk Mengisi):
    </p>
    <div class="grid grid-cols-3 gap-2 text-center">
        <button type="button" onclick="fillDemo('197501012000011001', 'admin123')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-sky-300 text-[11px] font-medium transition-colors">
            🔑 Admin
        </button>
        <button type="button" onclick="fillDemo('000000000000000001', 'admin123')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-indigo-300 text-[11px] font-medium transition-colors">
            ⚡ Superadmin
        </button>
        <button type="button" onclick="fillDemo('198001012005011002', '12345678')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-emerald-300 text-[11px] font-medium transition-colors">
            🎓 Peserta
        </button>
    </div>
</div>
@endif

<div class="mt-6 text-center text-xs text-white/50 space-y-2">
    <div>
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="text-sky-400 hover:underline font-semibold ml-1">Daftar Akun Baru</a>
    </div>
    <div>
        <a href="{{ url('/') }}" class="text-white/40 hover:text-white transition-colors text-[11px]">
            &larr; Kembali ke Beranda
        </a>
    </div>
</div>

@if(!app()->environment('production'))
<script>
function fillDemo(nip, pass) {
    document.getElementById('nip').value = nip;
    document.getElementById('password').value = pass;
}
</script>
@endif

@endsection
