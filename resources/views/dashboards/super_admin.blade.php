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
                <button type="button" id="start-camera-btn" class="bg-gray-800 text-white px-6 py-4 rounded-lg hover:bg-gray-900 transition flex items-center justify-center font-bold text-lg whitespace-nowrap">
                    📷 Camera
                </button>
            </div>
            
            <div id="reader-container" class="relative w-full mt-4 hidden overflow-hidden rounded-lg border-4 border-gray-800 bg-black min-h-[300px]">
                <div id="reader-placeholder" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none text-white">
                    <svg class="animate-spin h-8 w-8 text-white mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="font-bold animate-pulse text-lg tracking-wide">Initializing Camera System...</p>
                    <p class="text-sm mt-1 text-gray-400">Please allow camera permissions if prompted</p>
                </div>
                
                <!-- Laser Line -->
                <div id="scanner-laser" class="absolute left-0 top-0 w-full h-[3px] bg-teletalk-red shadow-[0_0_15px_4px_rgba(211,32,39,0.9)] z-20 pointer-events-none hidden"></div>
                <!-- Scanning Frame brackets -->
                <div class="absolute inset-4 border-2 border-dashed border-white/50 z-10 pointer-events-none hidden" id="scanner-frame"></div>
                
                <div id="reader" class="w-full"></div>
            </div>
            <style>
                @keyframes scanlaser { 0% { top: 0%; } 50% { top: 98%; } 100% { top: 0%; } }
                .laser-active { display: block !important; animation: scanlaser 2s linear infinite; }
            </style>
            
            <div id="search-results" class="hidden mt-4"></div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
             <h3 class="font-bold text-lg mb-4 text-gray-800">Live Organization Workload</h3>
             
             <!-- Workload Slider -->
             <div class="flex overflow-x-auto space-x-4 pb-4 snap-x">
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
        
        <div class="bg-white shadow rounded-lg p-6">
             <h3 class="font-bold text-lg mb-4 text-gray-800">System Management</h3>
             <div class="flex space-x-2">
                 <a href="{{ route('admin.users.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-black transition">
                     Manage Users
                 </a>
                 <a href="{{ route('admin.reports.index') }}" class="bg-teletalk-green text-white px-4 py-2 rounded hover:bg-green-800 transition">
                     View & Export Reports
                 </a>
             </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('omni-search');
            const resultsContainer = document.getElementById('search-results');
            
            // Webcam Logic
            let html5QrCode = null;
            const startCamBtn = document.getElementById('start-camera-btn');
            const readerContainer = document.getElementById('reader-container');
            const placeholder = document.getElementById('reader-placeholder');
            const laser = document.getElementById('scanner-laser');
            const scannerFrame = document.getElementById('scanner-frame');
            
            if(startCamBtn && readerContainer) {
                startCamBtn.addEventListener('click', () => {
                    if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
                    if (readerContainer.classList.contains('hidden')) {
                        readerContainer.classList.remove('hidden');
                        placeholder.classList.remove('hidden');
                        laser.classList.remove('laser-active');
                        scannerFrame.classList.add('hidden');
                        startCamBtn.innerHTML = '🛑 Stop';
                        
                        html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                html5QrCode.stop().then(() => {
                                    readerContainer.classList.add('hidden');
                                    laser.classList.remove('laser-active');
                                    scannerFrame.classList.add('hidden');
                                    startCamBtn.innerHTML = '📷 Camera';
                                    searchInput.value = decodedText;
                                    triggerSearch(decodedText);
                                });
                            },
                            (errorMessage) => { /* Ignore standard background errors */ }
                        ).then(() => {
                            placeholder.classList.add('hidden');
                            laser.classList.add('laser-active');
                            scannerFrame.classList.remove('hidden');
                        }).catch(err => {
                            console.log(err);
                            placeholder.innerHTML = '<p class="text-teletalk-red font-bold text-xl">Camera Access Denied</p>';
                        });
                    } else {
                        html5QrCode.stop().then(() => {
                            readerContainer.classList.add('hidden');
                            laser.classList.remove('laser-active');
                            scannerFrame.classList.add('hidden');
                            startCamBtn.innerHTML = '📷 Camera';
                        }).catch(err => console.log("Failed to stop scanner", err));
                    }
                });
            }

            if(searchInput && resultsContainer) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        let query = searchInput.value.trim();
                        triggerSearch(query);
                    }
                });
            }

            function triggerSearch(query) {
                if(query.length > 0) {
                    fetch(`/dak/search?tracking_id=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.style.display = 'block';
                            if(data.success) {
                                let currLoc = data.file.current_department ? data.file.current_department.name : 'Unknown';
                                let html = `
                                    <div class="flex justify-between items-start border-b pb-4 mb-4">
                                        <div>
                                            <h4 class="font-bold text-2xl text-teletalk-green mb-1">${data.file.tracking_id}</h4>
                                            <p class="text-gray-900 text-lg"><strong>Subject:</strong> ${data.file.subject}</p>
                                            <div class="mt-2 space-x-2">
                                                <span class="px-2 py-1 bg-gray-200 rounded text-sm font-bold text-gray-800">Status: ${data.file.status}</span>
                                                <span class="px-2 py-1 rounded text-sm font-bold border ${data.file.priority === 'Urgent' ? 'bg-teletalk-red text-white' : 'bg-yellow-100 text-yellow-800'}">Priority: ${data.file.priority}</span>
                                            </div>
                                        </div>
                                        <div class="text-right bg-white p-3 rounded shadow-sm border border-gray-200">
                                            <p class="text-sm text-gray-500 uppercase tracking-wide">Current Location</p>
                                            <p class="font-bold text-xl text-gray-900">${currLoc}</p>
                                        </div>
                                    </div>
                                `;
                                html += `<h5 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-widest">Movement History</h5>`;
                                html += `<div class="space-y-4 border-l-2 border-teletalk-green ml-3 relative">`;
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

                            if (typeof gsap !== 'undefined') {
                                gsap.fromTo(resultsContainer, 
                                    { y: -20, opacity: 0 }, 
                                    { y: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
                                );
                            }
                        });
                }
            }
        });
    </script>
</x-app-layout>
