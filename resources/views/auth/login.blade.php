<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login {{ $selectedRole->name }} — E-Learning Praktikum</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'display': ['Sora', 'sans-serif'],
                        'body': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'slide-in': 'slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                        'fade-up':  'fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
                    },
                    keyframes: {
                        slideIn: {
                            '0%':   { transform: 'translateX(60px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)',    opacity: '1' },
                        },
                        fadeUp: {
                            '0%':   { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)',    opacity: '1' },
                        },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Sora', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.12);
        }
    </style>
</head>

<body class="min-h-screen bg-gray-950 flex items-center justify-center p-4 overflow-hidden relative">

    {{-- Animated background blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-violet-700 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-sky-700 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:1s"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-up">

        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 border border-white/20 mb-4 hover:bg-white/20 transition-all">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 class="font-display text-2xl font-bold text-white">Masuk Sistem</h1>
            <p class="text-white/50 text-sm mt-1">Gunakan akun <b>{{ $selectedRole->name }}</b> Anda</p>
        </div>

        {{-- Alert Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-200 text-sm">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Main Glass Card --}}
        <div class="glass rounded-3xl p-8">

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Hidden input role — Penting untuk Controller --}}
                <input type="hidden" name="role_selected" value="{{ $selectedRole->slug }}">

                {{-- Role badge --}}
                <div class="flex justify-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 border border-white/20 text-white text-sm font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        {{ $selectedRole->name }}
                    </span>
                </div>

                {{-- Username --}}
                <div class="mb-4">
                    <label for="username" class="block text-white/70 text-xs font-medium mb-1.5 uppercase tracking-wider">
                        Username / NIM / NIP
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        placeholder="Masukkan identitas Anda"
                        class="input-field w-full px-4 py-3 rounded-xl bg-white/08 border border-white/20 text-white placeholder-white/30 text-sm transition-all focus:border-white/60 focus:bg-white/12"
                    >
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-white/70 text-xs font-medium mb-1.5 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-3 pr-10 rounded-xl bg-white/08 border border-white/20 text-white placeholder-white/30 text-sm transition-all focus:border-white/60 focus:bg-white/12"
                        >
                        <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80 transition-colors">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 checked:bg-violet-600 transition-all">
                        <span class="text-white/60 text-xs group-hover:text-white transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-white/60 text-xs hover:text-white transition-colors underline underline-offset-4">Lupa password?</a>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full py-3.5 rounded-xl font-display font-bold text-sm text-white bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 active:scale-[0.98] transition-all shadow-lg shadow-violet-900/40">
                    Masuk ke Dashboard
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-white/10"></div>
                <span class="text-white/40 text-[10px] uppercase tracking-widest font-bold">Atau</span>
                <div class="flex-1 h-px bg-white/10"></div>
            </div>

            {{-- Google OAuth --}}
            <form method="POST" action="{{ route('auth.google.redirect') }}">
                @csrf
                <input type="hidden" name="role_selected" value="{{ $selectedRole->slug }}">
                <button type="submit"
                    class="w-full py-3 rounded-xl flex items-center justify-center gap-3 bg-white/05 border border-white/10 text-white text-sm font-medium hover:bg-white/10 active:scale-[0.98] transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google Workspace
                </button>
            </form>

        </div>

        <p class="text-center text-white/30 text-xs mt-8">
            &copy; {{ date('Y') }} Laboratorium Informatika.
        </p>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        });
    </script>
</body>
</html>
