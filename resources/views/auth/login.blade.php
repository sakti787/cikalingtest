<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Toko Rukun Jaya</title>
    <!-- Screen Zoom Preference Script -->
    <script>
        if (localStorage.getItem('screen-zoom') === 'enlarged') {
            document.documentElement.classList.add('zoom-enlarged');
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            font-size: 0.9375rem;
        }

        html.zoom-enlarged {
            font-size: 1.125rem;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col md:flex-row">

    <!-- Left Side: Branding (hidden on mobile) -->
    <div class="hidden md:flex md:w-1/2 bg-[#15803D] flex-col justify-between p-16 text-white relative overflow-hidden">
        <!-- Decorative subtle background glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>

        <!-- Top spacing -->
        <div></div>

        <!-- Centered Logo and App Title -->
        <div class="flex flex-col items-center text-center z-10">
            <!-- White circle with logo text "mbg" -->
            <div
                class="w-24 h-24 rounded-full bg-white flex items-center justify-center text-4xl font-extrabold text-[#15803D] shadow-xl mb-6 tracking-tight">
                mbg
            </div>
            <h1 class="text-4xl font-bold tracking-tight mb-2">Toko Rukun Jaya</h1>
            <p class="text-green-100 text-lg">Sistem Informasi Manajemen Toko</p>
        </div>

        <!-- Bottom Tagline in green-200 -->
        <div class="text-center z-10">
            <p class="text-green-200 text-base font-medium tracking-wide">
                Digitalisasi operasional toko Anda
            </p>
        </div>
    </div>

    <!-- Right Side: Login Card -->
    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8 min-h-screen">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl border border-slate-100 shadow-sm md:shadow-md/5"
            x-data="{ role: 'pemilik', showPassword: false }">
            <div class="text-center mb-8">
                <h2 class="text-slate-900 text-[28px] font-extrabold tracking-tight">Masuk ke Sistem</h2>
                <p class="text-slate-500 mt-2 text-sm">Pilih peran Anda dan masukkan akun untuk melanjutkan</p>
            </div>

            <!-- Session / Error Alerts -->
            @if(session('timeout'))
                <div
                    class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3 text-sm">
                    <!-- Clock Icon -->
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    <div>
                        <span class="font-semibold block mb-0.5">Sesi Berakhir</span>
                        {{ session('timeout') }}
                    </div>
                </div>
            @endif

            @if(session('message'))
                <div
                    class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-start gap-3 text-sm">
                    <!-- Check Icon -->
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    <div>
                        <span class="font-semibold block mb-0.5">Informasi</span>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    <div class="flex items-center gap-2 mb-2 font-semibold text-red-950">
                        <span>⚠️</span>
                        <span>Terdapat {{ $errors->count() }} kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form (POST /login) -->
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-5" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf

                <div>
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username"
                        value="{{ old('username') }}" required class="input-field" placeholder="Masukkan username Anda">
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            autocomplete="current-password" required class="input-field pr-12" placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center p-1 cursor-pointer">
                            <!-- Eye Icon -->
                            <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                            </svg>
                            <!-- Eye Off Icon -->
                            <svg x-show="showPassword" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" x-bind:disabled="loading"
                    x-bind:class="loading ? 'opacity-75 cursor-not-allowed' : ''"
                    class="btn-primary w-full justify-center mt-2 cursor-pointer">
                    <span x-text="loading ? 'Masuk...' : 'Masuk ke Sistem'">Masuk ke Sistem</span>
                </button>
            </form>

        </div>
    </div>

</body>

</html>