<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = \App\Models\Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // শুধুমাত্র @teletalk.com.bd ইমেইল এলাও করার জন্য কাস্টম রুল
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                'unique:'.User::class,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@teletalk.com.bd')) {
                        $fail('Registration is restricted to official @teletalk.com.bd emails only.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'department_id' => ['required', 'exists:departments,id'], // রেজিস্ট্রেশনের সময় ডিপার্টমেন্ট সিলেক্ট করতে হবে
            'role' => ['required', 'in:staff,admin'], // সুপার অ্যাডমিন ড্যাশবোর্ড থেকে তৈরি হবে, সাধারণ ইউজাররা স্টাফ বা অ্যাডমিন হিসেবে জয়েন করবে
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
