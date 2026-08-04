<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-light font-sans p-4 sm:p-6">
        <div class="max-w-md w-full bg-white border border-gray-100 rounded-xl shadow-md p-6 sm:p-8">
            
            <!-- Logo & Title -->
            <div class="text-center mb-6 sm:mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-soft-green mb-3 sm:mb-4 overflow-hidden shadow-xs">
                    <img src="{{ asset('images/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-base-dark tracking-tight">Saung Aqiqah</h2>
                <p class="text-xs sm:text-sm text-base-medium mt-1">Sistem Penentuan Lokasi</p>
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
                    <input id="username" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2.5 px-3 min-h-[44px] transition-colors" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Masukkan username" />
                </div>

                <!-- Password -->
                <div class="mt-4 sm:mt-5" x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative mt-1">
                        <input id="password" :type="showPassword ? 'text' : 'password'" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2.5 pl-3 pr-10 min-h-[44px] transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.957 9.957 0 014.122-.963c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-1.782 1.782l-14-14m16.5 16.5l-14-14M15 12a3 3 0 11-4.243-4.243"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer min-h-[38px]">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary w-4 h-4" name="remember">
                        <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 min-h-[44px] border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
