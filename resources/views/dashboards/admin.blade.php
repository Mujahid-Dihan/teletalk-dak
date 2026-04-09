<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-bold text-xl text-teletalk-green">Admin Action Center : {{ auth()->user()->department->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 space-y-6" x-data="{ archiveModalOpen: false, archiveFileId: null, forwardModalOpen: false, forwardFileId: null }">
        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-teletalk-green">
            <div class="flex items-center space-x-2">
                <input type="text" id="omni-search" placeholder="Scan Barcode to Find File History..." class="w-full text-xl p-4 border-2 border-gray-300 rounded focus:border-teletalk-green">
                <button type="button" onclick="startCameraFor('omni-search')" class="scanner-btn-black w-20 h-20 rounded-[1.5rem] shadow-xl shrink-0 border-none outline-none group" title="Open QR Scanner">
                    <svg viewBox="0 0 24 24" class="h-10 w-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M 4 8 V 4 h 4" />
                        <path d="M 16 4 h 4 v 4" />
                        <path d="M 4 16 v 4 h 4" />
                        <path d="M 16 20 h 4 v -4" />
                        <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white" stroke-width="3" />
                    </svg>
                </button>
                <button type="button" onclick="triggerSearch(document.getElementById('omni-search').value.trim())" class="bg-teletalk-green text-white font-bold px-8 h-20 rounded-[1.5rem] hover:bg-green-800 transition shadow-xl text-xl shrink-0 border-none outline-none focus:ring-4 focus:ring-green-300">
                    Search
                </button>
            </div>
            <div id="search-results-container" class="hidden mt-4 bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-inner">
                <div id="search-results"></div>
                
                <div class="flex justify-end mt-6 border-t border-gray-200 pt-4">
                    <button onclick="const container = document.getElementById('search-results-container'); container.style.display = 'none'; container.classList.add('hidden')" 
                            class="flex items-center px-4 py-2 bg-white border border-red-200 text-red-600 font-medium rounded hover:bg-red-50 transition shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close Results
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <div class="flex items-center justify-between w-full mb-6 gap-6">
                
                <h3 class="font-bold text-lg text-gray-800 whitespace-nowrap shrink-0 leading-none">Files Requiring Your Action</h3>

                <!-- Search Container (Pulled to right, width restricted) -->
                <div class="flex items-center justify-end w-full max-w-md gap-3">
                    
                    <!-- Search Input Block (Inside Border) -->
                    <div class="flex items-center w-full border border-gray-300 rounded-lg bg-white focus-within:border-teletalk-green focus-within:ring-1 focus-within:ring-teletalk-green transition shadow-sm h-10 overflow-hidden">
                        
                        <!-- Search Icon -->
                        <div class="pl-3 pr-2 flex items-center justify-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        
                        <!-- Text Input -->
                        <input type="text" id="table-filter" placeholder="Search within table..." class="flex-1 w-full py-1 px-2 border-none border-transparent focus:border-transparent focus:ring-0 text-sm bg-transparent shadow-none outline-none text-gray-800">
                        
                        <!-- Scan Button -->
                        <button type="button" onclick="startCameraFor('table-filter')" class="scanner-btn-black w-10 h-10 rounded-lg shrink-0 border-none outline-none" title="Scan Barcode to Filter">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M 4 8 V 4 h 4" />
                                <path d="M 16 4 h 4 v 4" />
                                <path d="M 4 16 v 4 h 4" />
                                <path d="M 16 20 h 4 v -4" />
                                <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white" stroke-width="3" />
                            </svg>
                        </button>
                    </div>

                    <!-- Outside Submit Button -->
                    <button type="button" onclick="document.getElementById('table-filter').dispatchEvent(new Event('input'))" class="bg-teletalk-green text-white font-bold px-6 h-10 rounded-lg hover:bg-green-800 transition text-sm shrink-0 border-none outline-none shadow-sm">
                        Search
                    </button>
                    
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-600 border-b">
                        <tr><th class="p-3">Tracking ID</th><th class="p-3">Subject</th><th class="p-3">From (Initiator)</th><th class="p-3">Actions</th></tr>
                    </thead>
                    <tbody id="action-table-body">
                        @foreach($departmentFiles as $file)
                        <tr class="border-b hover:bg-gray-50 table-row-item">
                            <td class="p-3 font-mono font-bold">{{ $file->tracking_id }}</td>
                            <td class="p-3">{{ $file->subject }}</td>
                            <td class="p-3">{{ $file->originDepartment->name }}</td>
                            <td class="p-3 space-x-2">
                                <button @click="forwardModalOpen = true; forwardFileId = {{ $file->id }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Forward</button>
                                <button @click="archiveModalOpen = true; archiveFileId = {{ $file->id }}" class="bg-teletalk-green text-white px-3 py-1 rounded text-sm hover:bg-green-800">Mark as Done</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
        // ==========================================
        // 1. Table Filtering Logic (Manual Typing)
        // ==========================================
        document.getElementById('table-filter').addEventListener('input', function() {
            let filterText = this.value.toLowerCase();
            let rows = document.querySelectorAll('#action-table-body .table-row-item');
            let visibleCount = 0;

            rows.forEach(row => {
                let rowData = row.innerText.toLowerCase();
                if (rowData.includes(filterText)) {
                    row.style.visibility = 'visible';
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.visibility = 'hidden';
                    row.style.display = 'none';
                }
            });

            // Handle "No Results" message for table
            let noResultsRow = document.getElementById('no-results-row');
            if (visibleCount === 0) {
                if (!noResultsRow) {
                    let tableBody = document.getElementById('action-table-body');
                    let row = tableBody.insertRow();
                    row.id = 'no-results-row';
                    row.className = 'border-b text-center py-8 bg-gray-50';
                    row.innerHTML = `<td colspan="4" class="p-8"><div class="text-gray-400 font-bold uppercase tracking-widest text-sm">No Matching Files Found</div></td>`;
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        });

        // ==========================================
        // 2. Search Results Logic
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('omni-search');
            const resultsContainerWrapper = document.getElementById('search-results-container');
            const resultsContainer = document.getElementById('search-results');

            if(searchInput && resultsContainerWrapper) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        let query = searchInput.value.trim();
                        triggerSearch(query);
                    }
                });
            }

            function triggerSearch(query) {
                if(!query || query.length === 0) return;

                resultsContainerWrapper.classList.remove('hidden');
                resultsContainerWrapper.style.display = 'block';
                resultsContainer.innerHTML = `
                    <div class="flex items-center justify-center p-8 bg-gray-50 rounded-lg">
                        <svg class="animate-spin h-8 w-8 text-teletalk-green" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="ml-3 font-medium text-gray-500">Searching archive...</span>
                    </div>
                `;

                fetch(`/dak/search?tracking_id=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            let currLoc = data.file.current_department ? data.file.current_department.name : 'Unknown';
                            let html = `
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start border-b pb-4 mb-4">
                                        <div>
                                            <h4 class="font-bold text-2xl text-teletalk-green mb-1">${data.file.tracking_id}</h4>
                                            <p class="text-gray-900 text-lg"><strong>Subject:</strong> ${data.file.subject}</p>
                                            <div class="mt-2 space-x-2">
                                                <span class="px-2 py-1 bg-gray-200 rounded text-sm font-bold text-gray-800">Status: ${data.file.status}</span>
                                                <span class="px-2 py-1 rounded text-sm font-bold border ${data.file.priority === 'Urgent' ? 'bg-teletalk-red text-white' : 'bg-yellow-100 text-yellow-800'}">Priority: ${data.file.priority}</span>
                                            </div>
                                        </div>
                                        <div class="text-right bg-white p-3 rounded shadow-sm border border-gray-200 text-nowrap">
                                            <p class="text-sm text-gray-500 uppercase tracking-wide">Current Location</p>
                                            <p class="font-bold text-xl text-gray-900">${currLoc}</p>
                                        </div>
                                    </div>
                                    <h5 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-widest text-nowrap">Movement History</h5>
                                    <div class="space-y-4 border-l-2 border-teletalk-green ml-3 relative">
                            `;
                            
                            data.file.movements.forEach((movement) => {
                                let date = new Date(movement.created_at).toLocaleString();
                                let destination = movement.to_department ? movement.to_department.name : 'System / Archival';
                                html += `
                                    <div class="pl-6 relative">
                                        <div class="absolute w-3 h-3 bg-teletalk-green rounded-full -left-[7px] top-1.5 border-2 border-white"></div>
                                        <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="font-bold text-gray-900">${movement.action}</span>
                                                <span class="text-gray-500">${date}</span>
                                            </div>
                                            <p class="text-sm text-gray-700">Processed by <strong>${movement.user ? movement.user.name : "System"}</strong></p>
                                            ${movement.action === 'Forwarded' ? `<p class="text-sm text-blue-600 font-medium mt-1">&rarr; Sent to ${destination}</p>` : ''}
                                            ${movement.action === 'Completed' ? `<p class="text-sm text-gray-600 font-medium mt-1">&rarr; Locked at: ${data.file.physical_location}</p>` : ''}
                                            ${movement.remarks ? `<div class="mt-2 p-2 bg-gray-50 text-sm italic text-gray-600 border-l-2 border-gray-300">"${movement.remarks}"</div>` : ''}
                                        </div>
                                    </div>
                                `;
                            });
                            html += `</div></div>`;
                            resultsContainer.innerHTML = html;
                        } 
                        else {
                            resultsContainer.innerHTML = `
                                <div class="p-8 bg-red-50 border border-red-200 rounded-xl text-red-700 text-center shadow-inner">
                                    <svg class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="font-bold text-2xl mb-2">Record Not Found</p>
                                    <p class="text-gray-600 text-lg">"We couldn't find any file matching Tracking ID: <span class="text-red-600 font-mono font-bold px-2 py-1 bg-red-100 rounded">'${query}'</span>"</p>
                                    <p class="text-gray-500 mt-4 text-sm italic">Please verify the Tracking ID or scan the QR code again.</p>
                                </div>
                            `;
                        }

                        if (typeof gsap !== 'undefined') {
                            gsap.fromTo(resultsContainerWrapper, 
                                { y: -20, opacity: 0, display: 'block' }, 
                                { y: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
                            );
                        }
                    })
                    .catch(err => {
                        resultsContainer.innerHTML = `<div class="p-4 bg-red-100 text-red-700 rounded">Network error. Please try again later.</div>`;
                    });
            }

            // Expose for global scanner
            window.triggerSearch = triggerSearch;
        }); // end DOMContentLoaded
    </script>
</x-app-layout>
