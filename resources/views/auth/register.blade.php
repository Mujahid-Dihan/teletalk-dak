<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teletalk Dak Management System - Register</title>
    <link rel="icon" type="image/png" href="{{ asset('images/teletalk-logo.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-white via-green-50 to-green-100 font-sans antialiased selection:bg-teletalk-green selection:text-white py-10 px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-green-100 overflow-hidden my-auto">
        
        <div class="bg-gradient-to-b from-green-50 to-white px-8 pt-8 pb-6 text-center border-b border-green-50">
            <img src="{{ asset('images/teletalk-logo.png') }}" alt="Teletalk Logo" class="h-16 mx-auto mb-4">
            <h2 class="text-2xl font-bold text-teletalk-green">Dak Management</h2>
            <p class="text-sm text-gray-500 mt-1">Create your official Teletalk account</p>
        </div>

        <div class="p-8 pt-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition" 
                        placeholder="Your Full Name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Official Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition" 
                        placeholder="e.g., officer@teletalk.com.bd">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select id="department_id" name="department_id" required
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition">
                        <option value="">Select your Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition">
                        <option value="">Select your Role</option>
                        <option value="staff">Staff (Entry Level)</option>
                        <option value="admin">Admin (Department Head)</option>
                        <option value="viewer">General User (Viewer)</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50 transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-teletalk-green hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teletalk-green transition transform hover:-translate-y-0.5">
                    Register Account
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-8 py-5 text-center border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Already registered? 
                <a href="{{ route('login') }}" class="font-bold text-teletalk-green hover:text-green-800 transition hover:underline">
                    Log in here
                </a>
            </p>
        </div>
    </div>

</body>
</html>
