<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-bold text-xl text-teletalk-green">Staff Portal : {{ auth()->user()->department->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-[95%] mx-auto px-4 grid md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6 border-t-4 border-teletalk-green">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Initiate New Dak</h3>
            
            <form action="{{ route('dak.store') }}" method="POST" class="space-y-4">
                @csrf
                
            @if(session('new_qr_id'))
            <div class="bg-green-50 border-2 border-teletalk-green p-6 rounded-lg text-center mb-6">
                <h3 class="text-xl font-bold text-green-800 mb-2">File Initiated Successfully!</h3>
                <p class="text-gray-600 mb-4">Print this QR Code and stick it to the physical file.</p>
                
                <div class="bg-white inline-block p-4 rounded shadow-md">
                    {!! QrCode::size(150)->generate(session('new_qr_id')) !!}
                </div>
                
                <p class="mt-4 font-mono font-bold text-lg">{{ session('new_qr_id') }}</p>
                <button type="button" onclick="window.print()" class="mt-4 bg-gray-800 text-white px-4 py-2 rounded font-bold">Print QR Code</button>
            </div>
            @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50" placeholder="e.g., Leave Application">
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
                    <label class="block text-sm font-medium text-gray-700">Send To (Target Department)</label>
                    <select name="current_department_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                        <option value="">Select Department...</option>
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

        <div class="md:col-span-2 bg-white shadow rounded-lg p-6 overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg text-gray-800">My File Entries (Status)</h3>
            </div>
            
            <form method="GET" action="{{ route('dashboard') }}" class="mb-6 w-full relative" x-data="{ showFilters: false }">
                <div class="flex w-full items-center space-x-3">
                    
                    <div class="flex-1 flex items-center w-full bg-white border border-gray-300 rounded-xl shadow-sm focus-within:border-teletalk-green focus-within:ring focus-within:ring-teletalk-green focus-within:ring-opacity-50 transition text-lg overflow-hidden">
                        
                        <!-- Left Search Icon -->
                        <div class="pl-4 pr-3 text-gray-400 shrink-0 flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        
                        <!-- Main Input -->
                        <input type="text" id="staff-search" name="tracking_id" value="{{ request('tracking_id') }}" placeholder="Search files by Tracking ID..." class="flex-1 block w-full py-4 px-2 bg-transparent border-none border-transparent focus:border-transparent focus:ring-0 shadow-none outline-none">
                        
                        <!-- Scanner Button -->
                        <button type="button" onclick="startCameraFor('staff-search')" class="scanner-btn-black w-12 h-12 rounded-xl shrink-0 border-none outline-none mx-2 shadow-md" title="Open QR Scanner">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M 4 8 V 4 h 4" />
                                <path d="M 16 4 h 4 v 4" />
                                <path d="M 4 16 v 4 h 4" />
                                <path d="M 16 20 h 4 v -4" />
                                <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white" stroke-width="3" />
                            </svg>
                        </button>
                        
                        <!-- Right Filter Toggle (Replaces ⌘ K) -->
                        <div class="pr-3 flex items-center shrink-0">
                            <button type="button" @click="showFilters = !showFilters" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition focus:outline-none flex items-center justify-center" title="Advanced Filters">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            </button>
                        </div>

                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-teletalk-green text-white px-8 py-4 rounded-xl hover:bg-green-800 transition font-bold text-lg shrink-0 shadow-sm">
                        Search
                    </button>

                    @if(request()->hasAny(['tracking_id', 'date', 'start_time', 'end_time']))
                        <a href="{{ route('dashboard') }}" class="bg-red-50 text-red-600 px-6 py-4 rounded-xl border border-red-200 hover:bg-red-100 transition font-bold shrink-0 text-center shadow-sm" title="Clear Filters">
                            Clear
                        </a>
                    @endif
                </div>

                <!-- Dropdown Filters (Alpine) -->
                <div x-show="showFilters" x-transition style="display: none;" class="mt-2 bg-white p-5 rounded-lg border-2 border-gray-100 shadow-xl relative z-10 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ request('start_time') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ request('end_time') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                    </div>
                </div>
            </form>

            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead><tr class="bg-gray-100 text-gray-600 border-b text-sm"><th class="p-2">Tracking ID</th><th class="p-2">Date & Time</th><th class="p-2">Subject</th><th class="p-2">Location</th><th class="p-2">Status & Actions</th></tr></thead>
                <tbody class="text-sm">
                    @forelse($myEntries as $file)
                    <tr class="border-b">
                        <td class="p-2 font-mono text-teletalk-green whitespace-nowrap">{{ $file->tracking_id }}</td>
                        <td class="p-2 text-gray-500 whitespace-nowrap">{{ $file->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-2 whitespace-nowrap">{{ $file->subject }}</td>
                        <td class="p-2 whitespace-nowrap">{{ $file->currentDepartment->name }}</td>
                        <td class="p-2 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded {{ $file->status == 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $file->status }}
                            </span>
                            <button type="button" onclick="openPrintModal('{{ $file->tracking_id }}')" class="bg-gray-800 text-white px-2 py-1 rounded text-xs hover:bg-gray-900 transition ml-2">
                                Print QR
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="bg-gray-50 rounded-xl p-8 border-2 border-dashed border-gray-200 inline-block">
                                <svg class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">No Matching Entries Found</p>
                                <p class="text-gray-400 text-xs mt-1 italic">Try adjusting your filters or check the Tracking ID.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print QR Modal -->
    <div id="qrPrintModal" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-75 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full text-center shadow-2xl relative">
            <img src="{{ asset('images/teletalk-logo.png') }}" class="h-8 mx-auto mb-2" alt="Teletalk">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Official Dak Tracking Label</h2>
            <div id="qrcode-container" class="flex justify-center mb-4"></div>
            <p id="qr-tracking-text" class="text-2xl font-mono font-bold text-teletalk-green mb-6"></p>
            
            <div class="flex justify-between space-x-4 print-hide">
                <button type="button" onclick="closePrintModal()" class="w-full py-2 bg-gray-200 text-gray-800 rounded font-bold hover:bg-gray-300 transition">Cancel</button>
                <button type="button" onclick="window.print()" class="w-full py-2 bg-teletalk-green text-white rounded font-bold hover:bg-green-800 transition">Print Label</button>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * { visibility: hidden; }
            #qrPrintModal, #qrPrintModal * { visibility: visible; }
            #qrPrintModal { position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; padding: 0; box-shadow: none; display: flex !important; align-items: flex-start; justify-content: center; padding-top: 2cm;}
            #qrPrintModal .bg-white { box-shadow: none; max-w: none; border: 2px dashed #000; width: 80mm; padding: 5mm;}
            .print-hide { display: none !important; }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        function openPrintModal(trackingId) {
            document.getElementById('qrPrintModal').classList.remove('hidden');
            document.getElementById('qr-tracking-text').innerText = trackingId;
            document.getElementById('qrcode-container').innerHTML = '';
            new QRCode(document.getElementById("qrcode-container"), {
                text: trackingId,
                width: 150,
                height: 150,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
        function closePrintModal() {
            document.getElementById('qrPrintModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
