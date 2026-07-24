<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-white font-sans p-4">
        <div class="max-w-md w-full bg-white border border-gray-100 rounded-xl shadow-md p-8">
            
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-soft-green mb-4 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
                </div>
                <h2 class="text-2xl font-bold text-base-dark tracking-tight">Saung Aqiqah</h2>
                <p class="text-sm text-base-medium mt-1">Sistem Penentuan lokasi</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Single Error Alert -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-red-700">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input id="username" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 transition-colors" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Masukkan username" />
                </div>

                <!-- Password -->
                <div class="mt-5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-primary hover:text-primary-dark transition-colors" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="remember">
                        <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
