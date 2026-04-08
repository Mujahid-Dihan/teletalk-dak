<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-bold text-xl text-teletalk-green">Staff Portal : {{ auth()->user()->department->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-6">
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

        <div class="md:col-span-2 bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800">My File Entries (Status)</h3>
            <table class="w-full text-left border-collapse">
                <thead><tr class="bg-gray-100 text-gray-600 border-b"><th>Tracking ID</th><th>Subject</th><th>Current Location</th><th>Status & Actions</th></tr></thead>
                <tbody>
                    @foreach($myEntries as $file)
                    <tr class="border-b">
                        <td class="p-2 font-mono text-teletalk-green">{{ $file->tracking_id }}</td>
                        <td class="p-2">{{ $file->subject }}</td>
                        <td class="p-2">{{ $file->currentDepartment->name }}</td>
                        <td class="p-2">
                            <span class="px-2 py-1 text-xs rounded {{ $file->status == 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $file->status }}
                            </span>
                            <button type="button" onclick="openPrintModal('{{ $file->tracking_id }}')" class="bg-gray-800 text-white px-2 py-1 rounded text-xs hover:bg-gray-900 transition ml-2">
                                Print QR
                            </button>
                        </td>
                    </tr>
                    @endforeach
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

    <div id="reader-container" class="hidden fixed inset-0 z-50 bg-black bg-opacity-90 flex flex-col items-center justify-center p-4">
        <div id="reader" class="w-full max-w-md bg-white rounded-lg overflow-hidden"></div>
        <button id="stop-scan" type="button" class="mt-6 bg-red-600 text-white px-8 py-2 rounded-full font-bold">Close Scanner</button>
    </div>

    <script>
        let html5QrCode;

        document.getElementById('start-scan').addEventListener('click', function() {
            // স্ক্যানার কন্টেইনার দেখানো
            document.getElementById('reader-container').classList.remove('hidden');
            
            html5QrCode = new Html5Qrcode("reader");
            
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            // ক্যামেরা স্টার্ট করা
            html5QrCode.start(
                { facingMode: "environment" }, // পেছনের ক্যামেরা ব্যবহার করবে
                config,
                (decodedText, decodedResult) => {
                    // স্ক্যান সফল হলে যা হবে:
                    document.getElementById('omni-search').value = decodedText; // সার্চ বক্সে কোড বসানো
                    stopScanner(); // ক্যামেরা বন্ধ করা
                    
                    // অটোমেটিক সার্চ ট্রিগার করা (Enter কী প্রেস করার মতো)
                    const event = new KeyboardEvent('keypress', { key: 'Enter' });
                    document.getElementById('omni-search').dispatchEvent(event);
                },
                (errorMessage) => {
                    // স্ক্যানিং চলাকালীন এরর (সাধারণত ইগনোর করা হয়)
                }
            ).catch((err) => {
                alert("Camera permission denied or not found!");
                stopScanner();
            });
        });

        document.getElementById('stop-scan').addEventListener('click', stopScanner);

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('reader-container').classList.add('hidden');
                }).catch((err) => console.log(err));
            } else {
                document.getElementById('reader-container').classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
