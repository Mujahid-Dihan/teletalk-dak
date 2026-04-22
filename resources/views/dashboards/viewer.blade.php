<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-teletalk-green leading-tight">File Tracking Portal</h2>
    </x-slot>

    <div class="py-6 sm:py-12 max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">

        {{-- ==========================================
             SCAN OR SEARCH PANEL
        ========================================== --}}
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 border-l-4 border-teletalk-green mb-8">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Scan or Search File</h3>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Search Input --}}
                <input type="text" id="omni-search"
                    placeholder="Scan Barcode or Enter Tracking ID..."
                    class="flex-1 min-w-0 text-base sm:text-xl p-3 sm:p-4 border-2 border-gray-300 rounded focus:border-teletalk-green focus:ring-2 focus:ring-teletalk-green focus:ring-opacity-30 outline-none transition">

                {{-- Buttons --}}
                <div class="flex gap-3 shrink-0">
                    <button type="button" onclick="startCameraFor('omni-search')"
                        class="scanner-btn-black flex-1 sm:flex-none sm:w-14 h-14 sm:h-16 rounded-xl shadow-xl border-none outline-none"
                        title="Open QR Scanner">
                        <svg viewBox="0 0 24 24" class="h-8 w-8 sm:h-9 sm:w-9 text-white mx-auto" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M 4 8 V 4 h 4" />
                            <path d="M 16 4 h 4 v 4" />
                            <path d="M 4 16 v 4 h 4" />
                            <path d="M 16 20 h 4 v -4" />
                            <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white"
                                stroke-width="3" />
                        </svg>
                    </button>
                    <button type="button"
                        onclick="triggerSearch(document.getElementById('omni-search').value.trim())"
                        class="flex-1 sm:flex-none bg-teletalk-green text-white font-bold px-6 sm:px-8 h-14 sm:h-16 rounded-xl hover:bg-green-800 transition shadow-xl text-base sm:text-xl border-none outline-none focus:ring-4 focus:ring-green-300 whitespace-nowrap">
                        Search
                    </button>
                </div>
            </div>

            {{-- Search Results Container --}}
            <div id="search-results-container"
                class="hidden mt-4 bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-200 shadow-inner">
                <div id="search-results"></div>

                <div class="flex justify-end mt-6 border-t border-gray-200 pt-4">
                    <button
                        onclick="const container = document.getElementById('search-results-container'); container.style.display = 'none'; container.classList.add('hidden')"
                        class="flex items-center px-4 py-2 bg-white border border-red-200 text-red-600 font-medium rounded hover:bg-red-50 transition shadow-sm text-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close Results
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('omni-search');
            const resultsContainerWrapper = document.getElementById('search-results-container');
            const resultsContainer = document.getElementById('search-results');

            if (searchInput && resultsContainerWrapper) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        triggerSearch(searchInput.value.trim());
                    }
                });
            }

            function triggerSearch(query) {
                if (!query || query.length === 0) return;

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
                        if (data.success) {
                            let currLoc = data.file.current_department ? data.file.current_department.name : 'Unknown';
                            let html = `
                                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 border-b pb-4 mb-4">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-xl sm:text-2xl text-teletalk-green mb-1 break-all">${data.file.tracking_id}</h4>
                                            <p class="text-gray-900 text-base"><strong>Subject:</strong> ${data.file.subject}</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="px-2 py-1 bg-gray-200 rounded text-sm font-bold text-gray-800">Status: ${data.file.status}</span>
                                                <span class="px-2 py-1 rounded text-sm font-bold border ${data.file.priority === 'Urgent' ? 'bg-teletalk-red text-white' : 'bg-yellow-100 text-yellow-800'}">Priority: ${data.file.priority}</span>
                                            </div>
                                        </div>
                                        <div class="bg-white p-3 rounded shadow-sm border border-gray-200 shrink-0 sm:text-right">
                                            <p class="text-sm text-gray-500 uppercase tracking-wide">Current Location</p>
                                            <p class="font-bold text-lg sm:text-xl text-gray-900">${currLoc}</p>
                                        </div>
                                    </div>
                                    <h5 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-widest">Movement History</h5>
                                    <div class="space-y-4 border-l-2 border-teletalk-green ml-3 relative">
                            `;

                            data.file.movements.forEach((movement) => {
                                let date = new Date(movement.created_at).toLocaleString();
                                let destination = movement.to_department ? movement.to_department.name : 'System / Archival';
                                html += `
                                    <div class="pl-6 relative">
                                        <div class="absolute w-3 h-3 bg-teletalk-green rounded-full -left-[7px] top-1.5 border-2 border-white"></div>
                                        <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1 text-sm mb-1">
                                                <span class="font-bold text-gray-900">${movement.action}</span>
                                                <span class="text-gray-500 text-xs sm:text-sm">${date}</span>
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
                        } else {
                            resultsContainer.innerHTML = `
                                <div class="p-6 sm:p-8 bg-red-50 border border-red-200 rounded-xl text-red-700 text-center shadow-inner">
                                    <svg class="h-12 w-12 sm:h-16 sm:w-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="font-bold text-xl sm:text-2xl mb-2">Record Not Found</p>
                                    <p class="text-gray-600 text-base">We couldn't find any file matching Tracking ID: <span class="text-red-600 font-mono font-bold px-2 py-1 bg-red-100 rounded">'${query}'</span></p>
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

            window.triggerSearch = triggerSearch;
        });
    </script>
</x-app-layout>
