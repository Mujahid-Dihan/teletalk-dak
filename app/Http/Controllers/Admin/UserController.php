<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;

class UserController extends Controller
{
    public function index() {
        $users = User::with('department')->get();
        $departments = Department::all();
        return view('admin.users.index', compact('users', 'departments'));
    }

    public function update(Request $request, User $user) {
        $user->update($request->only(['role', 'department_id']));
        return back()->with('success', 'User updated successfully.');
    }

    public function approveUser($id) {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);
        return back()->with('success', 'User Approved!');
    }
}
