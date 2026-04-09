<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-teletalk-red">Super Admin Control Panel</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6 text-center border-t-4 border-teletalk-red">
                <h3 class="text-gray-500 font-bold uppercase">Total Active Files</h3>
                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $allActiveFiles }}</p>
            </div>
            </div>

        <!-- Omni-Search global tracker for Super Admin -->
        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-teletalk-green">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Global Tracker</h3>
            <div class="flex items-center space-x-2">
                <input type="text" id="omni-search" placeholder="Enter Global Tracking ID to investigate file routing..." class="w-full text-xl p-4 border-2 border-gray-300 rounded focus:border-teletalk-green">
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

        <div class="bg-white shadow rounded-lg p-6">
             <h3 class="font-bold text-lg mb-4 text-gray-800">Live Organization Workload</h3>
             
             <!-- Workload Grid (Vertical) -->
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                 @foreach($departments as $dept)
                     @php
                         $count = isset($departmentWorkloads[$dept->id]) ? $departmentWorkloads[$dept->id]->total : 0;
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
                     <div class="border rounded-lg p-4 shadow-sm {{ $bgClass }} transition hover:shadow-md">
                         <h4 class="text-sm font-medium text-gray-600 truncate">{{ $dept->name }}</h4>
                         <div class="mt-2 flex items-baseline">
                             <span class="text-3xl font-bold {{ $textClass }}">{{ $count }}</span>
                             <span class="ml-2 text-sm text-gray-500">active files</span>
                         </div>
                     </div>
                 @endforeach
             </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-8 flex flex-col items-center text-center">
             <h3 class="font-bold text-2xl mb-6 text-gray-800">System Management</h3>
             <div class="flex flex-wrap justify-center gap-4">
                 <a href="{{ route('admin.users.index') }}" class="relative bg-gray-800 text-white px-8 py-3 rounded hover:bg-black transition font-bold shadow-lg">
                     Manage Users
                     @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full border-2 border-white animate-bounce">
                            {{ $pendingUsersCount }}
                        </span>
                     @endif
                 </a>
                 <a href="{{ route('admin.reports.index') }}" class="bg-teletalk-green text-white px-8 py-3 rounded hover:bg-green-800 transition font-bold shadow-lg">
                     View & Export Reports
                 </a>
             </div>
        </div>
    </div>
    
    <script>
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
                        <span class="ml-3 font-medium text-gray-500">Scanning Global Archive...</span>
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
                                    <h5 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-widest text-nowrap">Global Audit Trail</h5>
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
                                    <p class="font-bold text-2xl mb-2">Audit Entry Not Found</p>
                                    <p class="text-gray-600 text-lg">"Tracking ID: <span class="text-red-600 font-mono font-bold px-2 py-1 bg-red-100 rounded">'${query}'</span> does not exist in the organizational Dak database."</p>
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

            window.triggerSearch = triggerSearch;
        });
    </script>
</x-app-layout>
