<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-bold text-xl text-teletalk-green leading-tight">Staff Portal :
                <span class="block sm:inline text-lg font-semibold">{{ auth()->user()->department->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 space-y-6">

        {{-- ==========================================
             TWO COLUMN LAYOUT: FORM + TABLE
        ========================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- ==========================================
                 INITIATE NEW DAK FORM
            ========================================== --}}
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 border-t-4 border-teletalk-green">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Initiate New Dak</h3>

                {{-- QR Success Print Block --}}
                @if(session('new_qr_id'))
                    <div class="bg-green-50 border-2 border-teletalk-green p-4 sm:p-6 rounded-lg text-center mb-6">
                        <h3 class="text-lg sm:text-xl font-bold text-green-800 mb-2">File Initiated Successfully!</h3>
                        <p class="text-gray-600 mb-4 text-sm">Print this QR Code and stick it to the physical file.</p>
                        <div class="bg-white inline-block p-4 rounded shadow-md">
                            {!! QrCode::size(150)->generate(session('new_qr_id')) !!}
                        </div>
                        <p class="mt-4 font-mono font-bold text-base break-all">{{ session('new_qr_id') }}</p>
                        <button type="button" onclick="window.print()"
                            class="mt-4 bg-gray-800 text-white px-4 py-2 rounded font-bold text-sm hover:bg-gray-900 transition">Print QR
                            Code</button>
                    </div>
                @endif

                <form action="{{ route('dak.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50"
                            placeholder="e.g., Leave Application">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                            <option value="Normal">Normal</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Send To (Target Department)</label>
                        <select name="current_department_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                            <option value="">Select Department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full bg-teletalk-green text-white font-bold px-4 rounded hover:bg-green-800 transition h-12 sm:h-14 flex items-center justify-center">
                        Dispatch File
                    </button>
                </form>
            </div>

            {{-- ==========================================
                 MY FILE ENTRIES TABLE
            ========================================== --}}
            <div class="md:col-span-2 bg-white shadow rounded-lg p-4 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-gray-800">My File Entries (Status)</h3>
                </div>

                {{-- Search & Filter Form --}}
                <form method="GET" action="{{ route('dashboard') }}" class="mb-8 w-full relative"
                    x-data="{ showFilters: false }">
                    <div class="flex flex-col sm:flex-row w-full items-stretch sm:items-center gap-3">

                        {{-- Input + Scanner wrapped in bordered block --}}
                        <div
                            class="flex flex-1 min-w-0 items-center bg-white border border-gray-300 rounded-xl shadow-sm focus-within:border-teletalk-green focus-within:ring focus-within:ring-teletalk-green focus-within:ring-opacity-50 transition overflow-hidden h-12 sm:h-14">
                            <div class="pl-3 sm:pl-4 pr-2 text-gray-400 shrink-0 flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="staff-search" name="tracking_id"
                                value="{{ request('tracking_id') }}"
                                placeholder="Search files by Tracking ID..."
                                class="flex-1 min-w-0 block w-full py-2 px-2 bg-transparent border-none focus:border-transparent focus:ring-0 shadow-none outline-none text-sm sm:text-base">
                            <button type="button" onclick="startCameraFor('staff-search')"
                                class="scanner-btn-black w-10 sm:w-12 h-12 sm:h-14 rounded-none shrink-0 border-none outline-none"
                                title="Open QR Scanner">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 text-white mx-auto" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M 4 8 V 4 h 4" />
                                    <path d="M 16 4 h 4 v 4" />
                                    <path d="M 4 16 v 4 h 4" />
                                    <path d="M 16 20 h 4 v -4" />
                                    <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white"
                                        stroke-width="3" />
                                </svg>
                            </button>
                            <div class="pr-2 flex items-center shrink-0">
                                <button type="button" @click="showFilters = !showFilters"
                                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition focus:outline-none"
                                    title="Advanced Filters">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 shrink-0">
                            <button type="submit"
                                class="flex-1 sm:flex-none flex items-center justify-center bg-teletalk-green text-white px-5 sm:px-8 h-12 sm:h-14 rounded-xl hover:bg-green-800 transition font-bold text-sm sm:text-base shadow-sm">
                                Search
                            </button>
                            @if(request()->hasAny(['tracking_id', 'date', 'start_time', 'end_time']))
                                <a href="{{ route('dashboard') }}"
                                    class="flex-1 sm:flex-none flex items-center justify-center bg-red-50 text-red-600 px-4 sm:px-6 h-12 sm:h-14 rounded-xl border border-red-200 hover:bg-red-100 transition font-bold shadow-sm text-sm"
                                    title="Clear Filters">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Advanced Filters Dropdown --}}
                    <div x-show="showFilters" x-transition style="display: none;"
                        class="mt-3 bg-white p-4 sm:p-5 rounded-lg border-2 border-gray-100 shadow-xl relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                            <input type="date" name="date" value="{{ request('date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" value="{{ request('start_time') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time" value="{{ request('end_time') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green">
                        </div>
                    </div>
                </form>

                {{-- Desktop Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[580px]">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 border-b text-sm">
                                <th class="p-2 font-semibold">Tracking ID</th>
                                <th class="p-2 font-semibold">Date &amp; Time</th>
                                <th class="p-2 font-semibold">Subject</th>
                                <th class="p-2 font-semibold">Location</th>
                                <th class="p-2 font-semibold">Status &amp; Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($myEntries as $file)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-mono text-teletalk-green whitespace-nowrap text-xs">
                                        {{ $file->tracking_id }}</td>
                                    <td class="p-2 text-gray-500 whitespace-nowrap text-xs">
                                        {{ $file->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="p-2 text-sm">{{ $file->subject }}</td>
                                    <td class="p-2 whitespace-nowrap text-sm">{{ $file->currentDepartment->name }}</td>
                                    <td class="p-2 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs rounded {{ $file->status == 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $file->status }}
                                        </span>
                                        <button type="button" onclick="openPrintModal('{{ $file->tracking_id }}')"
                                            class="bg-gray-800 text-white px-2 py-1 rounded text-xs hover:bg-gray-900 transition ml-2">
                                            Print QR
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-10 text-center">
                                        <div
                                            class="bg-gray-50 rounded-xl p-8 border-2 border-dashed border-gray-200 inline-block">
                                            <svg class="h-10 w-10 mx-auto mb-3 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">No
                                                Matching Entries Found</p>
                                            <p class="text-gray-400 text-xs mt-1 italic">Try adjusting your filters or
                                                check the Tracking ID.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card List --}}
                <div class="sm:hidden space-y-3">
                    @forelse($myEntries as $file)
                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 shadow-sm space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-mono font-bold text-xs text-teletalk-green truncate">{{ $file->tracking_id }}</p>
                                    <p class="text-sm text-gray-800 mt-0.5 leading-snug">{{ $file->subject }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $file->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="shrink-0 text-xs bg-gray-200 text-gray-700 font-semibold px-2 py-1 rounded">{{ $file->currentDepartment->name }}</span>
                            </div>
                            <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                                <span class="text-xs px-2 py-1 rounded {{ $file->status == 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} font-semibold">
                                    {{ $file->status }}
                                </span>
                                <button type="button" onclick="openPrintModal('{{ $file->tracking_id }}')"
                                    class="bg-gray-800 text-white px-3 py-1.5 rounded text-xs hover:bg-gray-900 transition font-semibold ml-auto">
                                    Print QR
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50 rounded-xl p-8 border-2 border-dashed border-gray-200 text-center">
                            <svg class="h-10 w-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">No Matching Entries Found</p>
                            <p class="text-gray-400 text-xs mt-1 italic">Try adjusting your filters or check the Tracking ID.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Print QR Modal --}}
    <div id="qrPrintModal"
        class="fixed inset-0 z-50 bg-gray-900 bg-opacity-75 flex justify-center items-center hidden p-4">
        <div class="bg-white rounded-lg p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl relative">
            <img src="{{ asset('images/teletalk-logo.png') }}" class="h-8 mx-auto mb-2" alt="Teletalk">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Official Dak Tracking Label</h2>
            <div id="qrcode-container" class="flex justify-center mb-4"></div>
            <p id="qr-tracking-text" class="text-xl sm:text-2xl font-mono font-bold text-teletalk-green mb-6 break-all"></p>

            <div class="flex flex-col sm:flex-row gap-3 print-hide">
                <button type="button" onclick="closePrintModal()"
                    class="w-full py-2.5 bg-gray-200 text-gray-800 rounded font-bold hover:bg-gray-300 transition text-sm">Cancel</button>
                <button type="button" onclick="window.print()"
                    class="w-full py-2.5 bg-teletalk-green text-white rounded font-bold hover:bg-green-800 transition text-sm">Print
                    Label</button>
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
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
        function closePrintModal() {
            document.getElementById('qrPrintModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
