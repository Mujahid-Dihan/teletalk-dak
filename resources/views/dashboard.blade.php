<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dak Management Command Center : ') }} <span class="text-teletalk-green">{{ auth()->user()->department->name ?? 'Admin' }}</span>
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ archiveModalOpen: false, archiveFileId: null, forwardModalOpen: false, forwardFileId: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-teletalk-green text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <label for="omni-search" class="block text-sm font-medium text-gray-700 mb-2">Scan Barcode or Enter Tracking ID</label>
                    <input type="text" id="omni-search" autofocus autocomplete="off"
                        class="w-full text-2xl font-bold p-4 border-2 border-teletalk-green rounded-lg focus:ring-teletalk-green focus:border-teletalk-green" 
                        placeholder="e.g. TTBL-001234">
                    
                    <div id="search-results" class="hidden mt-4 p-4 bg-gray-50 border border-gray-300 rounded-lg">
                        </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Live Organization Workload</h3>
                <div class="flex overflow-x-auto space-x-4 pb-4 snap-x">
                    @foreach($departments as $dept)
                        @php
                            // Check if this department has active files in our workload array
                            $count = isset($departmentWorkloads[$dept->id]) ? $departmentWorkloads[$dept->id]->total : 0;
                            
                            // Determine color based on workload
                            $bgClass = 'bg-white';
                            $textClass = 'text-gray-900';
                            if ($count > 10) {
                                $bgClass = 'bg-red-50 border-teletalk-red';
                                $textClass = 'text-teletalk-red';
                            } elseif ($count > 0) {
                                $bgClass = 'bg-green-50 border-teletalk-green';
                                $textClass = 'text-teletalk-green';
                            }
                        @endphp
                        
                        <div class="min-w-[200px] flex-shrink-0 snap-start border rounded-lg p-4 shadow-sm {{ $bgClass }} transition hover:shadow-md">
                            <h4 class="text-sm font-medium text-gray-600 truncate">{{ $dept->name }}</h4>
                            <div class="mt-2 flex items-baseline">
                                <span class="text-3xl font-bold {{ $textClass }}">{{ $count }}</span>
                                <span class="ml-2 text-sm text-gray-500">active files</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Active Files at your Desk</h3>
                    
                    @if($myFiles->isEmpty())
                        <p class="text-gray-500 italic">No pending files at your desk. Great job!</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="border-b bg-gray-50 text-gray-600 font-medium">
                                    <tr>
                                        <th class="px-4 py-3">Tracking ID</th>
                                        <th class="px-4 py-3">Subject</th>
                                        <th class="px-4 py-3">Priority</th>
                                        <th class="px-4 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myFiles as $file)
                                        <tr class="border-b hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-mono font-bold text-teletalk-green">{{ $file->tracking_id }}</td>
                                            <td class="px-4 py-3">{{ $file->subject }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded text-xs font-bold 
                                                    {{ $file->priority == 'Urgent' ? 'bg-teletalk-red text-white' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $file->priority }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right space-x-2">
                                                <button @click="forwardModalOpen = true; forwardFileId = {{ $file->id }}" 
                                                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                                    Forward
                                                </button>
                                                
                                                <button @click="archiveModalOpen = true; archiveFileId = {{ $file->id }}" 
                                                    class="text-sm bg-gray-800 text-white px-3 py-1 rounded hover:bg-gray-700 transition">
                                                    Archive
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Initiate New Dak</h3>
                    <form action="{{ route('dak.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Tracking ID</label>
                            <input type="text" name="tracking_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Send To (Department)</label>
                            <select name="current_department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-teletalk-green text-white font-bold py-2 px-4 rounded hover:bg-green-800 transition">
                            Dispatch File
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="archiveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="archiveModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="archiveModalOpen" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/dak/' + archiveFileId + '/archive'" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Archive File</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">Please input the physical storage location (e.g., Cabinet 4, Shelf B) so this file can be retrieved in future audits.</p>
                                <input type="text" name="physical_location" required placeholder="Physical Location" class="w-full border-gray-300 rounded-md shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teletalk-green text-base font-medium text-white hover:bg-green-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Confirm Archival
                            </button>
                            <button @click="archiveModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="forwardModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="forwardModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="forwardModalOpen" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/dak/' + forwardFileId + '/forward'" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4">Forward File</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Department</label>
                                    <select name="target_department_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Remarks / Note (Optional)</label>
                                    <textarea name="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50" placeholder="e.g., Please review the attached BTRC compliance forms."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Dispatch Forward
                            </button>
                            <button @click="forwardModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('omni-search');
            const resultsContainer = document.getElementById('search-results');

            // Barcode scanners act like a keyboard and press "Enter" automatically at the end of a scan
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let query = searchInput.value.trim();
                    
                    if(query.length > 0) {
                        fetch(`/dak/search?tracking_id=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                resultsContainer.style.display = 'block';
                                
                                if(data.success) {
                                    // 1. Build the Header
                                    let html = `
                                        <div class="flex justify-between items-start border-b pb-4 mb-4">
                                            <div>
                                                <h4 class="font-bold text-2xl text-teletalk-green mb-1">${data.file.tracking_id}</h4>
                                                <p class="text-gray-900 text-lg"><strong>Subject:</strong> ${data.file.subject}</p>
                                                <div class="mt-2 space-x-2">
                                                    <span class="px-2 py-1 bg-gray-200 rounded text-sm font-bold text-gray-800">Status: ${data.file.status}</span>
                                                    <span class="px-2 py-1 rounded text-sm font-bold ${data.file.priority === 'Urgent' ? 'bg-teletalk-red text-white' : 'bg-yellow-100 text-yellow-800'}">Priority: ${data.file.priority}</span>
                                                </div>
                                            </div>
                                            <div class="text-right bg-white p-3 rounded shadow-sm border border-gray-200">
                                                <p class="text-sm text-gray-500 uppercase tracking-wide">Current Location</p>
                                                <p class="font-bold text-xl text-gray-900">${data.file.current_department.name}</p>
                                            </div>
                                        </div>
                                    `;

                                    // 2. Build the Audit Trail Timeline
                                    html += `<h5 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-widest">Movement History</h5>`;
                                    html += `<div class="space-y-4 border-l-2 border-teletalk-green ml-3 relative">`;
                                    
                                    data.file.movements.forEach((movement, index) => {
                                        // Format the date nicely
                                        let date = new Date(movement.created_at).toLocaleString();
                                        
                                        // Formatting the destination logic
                                        let destination = movement.to_department ? movement.to_department.name : 'System / Archival';
                                        
                                        html += `
                                            <div class="pl-6 relative">
                                                <div class="absolute w-3 h-3 bg-teletalk-green rounded-full -left-[7px] top-1.5 border-2 border-white"></div>
                                                
                                                <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                                    <div class="flex justify-between text-sm mb-1">
                                                        <span class="font-bold text-gray-900">${movement.action}</span>
                                                        <span class="text-gray-500">${date}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700">Processed by <strong>${movement.user.name}</strong></p>
                                                    
                                                    ${movement.action === 'Forwarded' ? `<p class="text-sm text-blue-600 font-medium mt-1">&rarr; Sent to ${destination}</p>` : ''}
                                                    ${movement.action === 'Completed' ? `<p class="text-sm text-gray-600 font-medium mt-1">&rarr; Locked at: ${data.file.physical_location}</p>` : ''}
                                                    
                                                    ${movement.remarks ? `<div class="mt-2 p-2 bg-gray-50 text-sm italic text-gray-600 border-l-2 border-gray-300">"${movement.remarks}"</div>` : ''}
                                                </div>
                                            </div>
                                        `;
                                    });
                                    html += `</div>`;

                                    resultsContainer.innerHTML = html;
                                } else {
                                    resultsContainer.innerHTML = `<p class="text-teletalk-red font-bold p-4 bg-red-50 border border-red-200 rounded">${data.message}</p>`;
                                }

                                // GSAP Animation for the results popping in
                                gsap.fromTo(resultsContainer, 
                                    { y: -20, opacity: 0 }, 
                                    { y: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
                                );
                                
                                // Optional: Clear the input after a short delay so they can scan the next file
                                setTimeout(() => { searchInput.value = ''; }, 3000);
                            });
                    }
                }
            });
        });
    </script>
</x-app-layout>
