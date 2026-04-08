<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-teletalk-green">File Tracking Portal</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4">
        <h3 class="font-bold text-lg mb-4 text-gray-800">My Applications Status</h3>
        
        <div class="bg-white shadow rounded-lg p-6">
            @if(isset($myFiles) && $myFiles->count() > 0)
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 border-b">
                            <th class="p-3">Tracking ID</th>
                            <th class="p-3">Subject</th>
                            <th class="p-3">Current Location</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myFiles as $file)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-mono font-bold">{{ $file->tracking_id }}</td>
                                <td class="p-3">{{ $file->subject }}</td>
                                <td class="p-3">{{ $file->currentDepartment->name ?? 'System' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $file->status == 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $file->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">No applications found.</p>
            @endif
        </div>
    </div>
</x-app-layout>
