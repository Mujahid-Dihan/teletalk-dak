<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-teletalk-green">Manage Users</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 border-b">
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Department</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <td class="p-3 font-bold">{{ $user->name }}</td>
                            <td class="p-3">{{ $user->email }}</td>
                            <td class="p-3">
                                <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                    <option value="staff" {{ in_array($user->role, ['Staff', 'staff']) ? 'selected' : '' }}>Staff</option>
                                    <option value="admin" {{ in_array($user->role, ['Admin', 'admin']) ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ in_array($user->role, ['Super Admin', 'super_admin']) ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            </td>
                            <td class="p-3">
                                <select name="department_id" class="rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                    <option value="">No Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-3 flex space-x-2">
                                <button type="submit" class="bg-teletalk-green text-white px-4 py-2 rounded hover:bg-green-800 transition font-bold">Update</button>
                        </form>
                                @if(!$user->is_approved)
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800 transition font-bold">Approve</button>
                                </form>
                                @else
                                <span class="bg-gray-100 text-green-700 px-3 py-2 rounded font-bold">Approved</span>
                                @endif

                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800 transition font-bold" title="Delete User">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
