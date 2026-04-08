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
                <button type="button" id="start-camera-btn" class="bg-gray-800 text-white px-6 py-4 rounded-lg hover:bg-gray-900 transition flex items-center justify-center font-bold text-lg whitespace-nowrap">
                    📷 Camera
                </button>
            </div>
            <div id="reader" class="w-full mt-4 hidden overflow-hidden rounded-lg border-2 border-dashed border-gray-300"></div>
            <div id="search-results" class="hidden mt-4"></div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Files Requiring Your Action</h3>
            <table class="w-full text-left border-collapse">
                <thead><tr class="bg-gray-100 text-gray-600 border-b"><th>Tracking ID</th><th>Subject</th><th>From (Initiator)</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($departmentFiles as $file)
                    <tr class="border-b hover:bg-gray-50">
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
        
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('omni-search');
            const resultsContainer = document.getElementById('search-results');
            
            // Webcam Logic
            let html5QrCode = null;
            const startCamBtn = document.getElementById('start-camera-btn');
            const readerDiv = document.getElementById('reader');
            
            if(startCamBtn && readerDiv) {
                startCamBtn.addEventListener('click', () => {
                    if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
                    if (readerDiv.classList.contains('hidden')) {
                        readerDiv.classList.remove('hidden');
                        startCamBtn.innerHTML = '🛑 Stop';
                        html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                html5QrCode.stop().then(() => {
                                    readerDiv.classList.add('hidden');
                                    startCamBtn.innerHTML = '📷 Camera';
                                    searchInput.value = decodedText;
                                    triggerSearch(decodedText);
                                });
                            },
                            (errorMessage) => { /* Ignore standard background errors */ }
                        ).catch(err => console.log(err));
                    } else {
                        html5QrCode.stop().then(() => {
                            readerDiv.classList.add('hidden');
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
