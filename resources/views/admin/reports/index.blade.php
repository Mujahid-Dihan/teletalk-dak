<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-teletalk-green">Audit Logs & Reports</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4">
        <div class="bg-white shadow rounded-lg p-6">
            
            <div class="flex justify-between items-center mb-6">
                <div class="space-x-2">
                    <a href="{{ route('admin.reports.index', ['type' => 'daily']) }}" class="px-4 py-2 rounded {{ $type == 'daily' ? 'bg-teletalk-green text-white' : 'bg-gray-200 text-gray-700' }}">Daily</a>
                    <a href="{{ route('admin.reports.index', ['type' => 'weekly']) }}" class="px-4 py-2 rounded {{ $type == 'weekly' ? 'bg-teletalk-green text-white' : 'bg-gray-200 text-gray-700' }}">Weekly</a>
                    <a href="{{ route('admin.reports.index', ['type' => 'monthly']) }}" class="px-4 py-2 rounded {{ $type == 'monthly' ? 'bg-teletalk-green text-white' : 'bg-gray-200 text-gray-700' }}">Monthly</a>
                </div>

                <a href="{{ route('admin.reports.index', ['type' => $type, 'download' => 'pdf']) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download {{ ucfirst($type) }} PDF
                </a>
            </div>

            <h3 class="text-lg font-bold mb-4">{{ $title }}</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border border-gray-200 min-w-[800px]">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border">Date & Time</th>
                            <th class="p-3 border">Tracking ID</th>
                            <th class="p-3 border">Action By</th>
                            <th class="p-3 border">Movement</th>
                            <th class="p-3 border">Action / Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            <td class="p-3 border font-mono">{{ $log->dakFile->tracking_id }}</td>
                            <td class="p-3 border">{{ $log->user->name }} ({{ $log->user->department->name ?? 'N/A' }})</td>
                            <td class="p-3 border">
                                {{ $log->fromDepartment->name ?? 'System' }} 
                                <span class="text-teletalk-green font-bold">→</span> 
                                {{ $log->toDepartment->name ?? 'Completed' }}
                            </td>
                            <td class="p-3 border">
                                @php
                                    $displayStatus = $log->dakFile->status === 'Completed' ? 'Done' : 'Pending';
                                    $badgeColor = $displayStatus === 'Done' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded font-bold {{ $badgeColor }}">{{ $displayStatus }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">No records found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
