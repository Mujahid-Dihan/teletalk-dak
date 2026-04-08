<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teletalk Dak Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-white via-green-50 to-green-100 font-sans antialiased selection:bg-teletalk-green selection:text-white">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-green-100 overflow-hidden">
        
        <div class="bg-gradient-to-b from-green-50 to-white px-8 pt-8 pb-6 text-center border-b border-green-50">
            <img src="{{ asset('images/teletalk-logo.png') }}" alt="Teletalk Logo" class="h-16 mx-auto mb-4">
            <h2 class="text-2xl font-bold text-teletalk-green">Dak Management</h2>
            <p class="text-sm text-gray-500 mt-1">Sign in to your official Teletalk account</p>
        </div>

        <div class="p-8 pt-6">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <x-input-error :messages="$errors->get('email')" class="mb-4 text-center" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Official Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition" 
                        placeholder="e.g., officer@teletalk.com.bd">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-teletalk-green shadow-sm focus:ring-teletalk-green">
                        <span class="ml-2 text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="font-medium text-teletalk-green hover:text-green-800 transition" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-teletalk-green hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teletalk-green transition transform hover:-translate-y-0.5">
                    Log in to Dashboard
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-8 py-5 text-center border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Don't have an official account? 
                <a href="{{ route('register') }}" class="font-bold text-teletalk-green hover:text-green-800 transition hover:underline">
                    Register here
                </a>
            </p>
        </div>
    </div>

</body>
</html>
