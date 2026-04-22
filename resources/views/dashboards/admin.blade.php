<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-bold text-xl text-teletalk-green leading-tight">Admin Action Center:
                <span class="block sm:inline text-lg font-semibold">{{ auth()->user()->department->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 space-y-6"
        x-data="{ archiveModalOpen: false, archiveFileId: null, forwardModalOpen: false, forwardFileId: null, pdfModalOpen: false, pdfFileId: null }">

        {{-- ==========================================
             OMNI SEARCH / BARCODE SECTION
        ========================================== --}}
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 border-l-4 border-teletalk-green">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Search Input --}}
                <input type="text" id="omni-search" placeholder="Scan Barcode or Enter Tracking ID..."
                    class="flex-1 min-w-0 text-base sm:text-xl p-3 sm:p-4 border-2 border-gray-300 rounded focus:border-teletalk-green focus:ring-2 focus:ring-teletalk-green focus:ring-opacity-30 outline-none transition">

                {{-- Buttons Row --}}
                <div class="flex gap-3 shrink-0">
                    {{-- Scanner Button --}}
                    <button type="button" onclick="startCameraFor('omni-search')"
                        class="scanner-btn-black flex-1 sm:flex-none w-full sm:w-14 h-14 sm:h-16 rounded-xl shadow-xl border-none outline-none group"
                        title="Open QR Scanner">
                        <svg viewBox="0 0 24 24" class="h-8 w-8 sm:h-9 sm:w-9 text-white mx-auto" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M 4 8 V 4 h 4" />
                            <path d="M 16 4 h 4 v 4" />
                            <path d="M 4 16 v 4 h 4" />
                            <path d="M 16 20 h 4 v -4" />
                            <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white"
                                stroke-width="3" />
                        </svg>
                    </button>

                    {{-- Search Button --}}
                    <button type="button" onclick="triggerSearch(document.getElementById('omni-search').value.trim())"
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

        {{-- ==========================================
             FILES REQUIRING ACTION TABLE
        ========================================== --}}
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 mt-6">

            {{-- Table Header + Filter --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="font-bold text-lg text-gray-800 leading-none shrink-0">Files Requiring Your Action</h3>

                {{-- Filter Input Row --}}
                <div class="flex items-center gap-2 w-full sm:max-w-md">
                    {{-- Input with icon --}}
                    <div
                        class="flex items-center flex-1 min-w-0 border border-gray-300 rounded-lg bg-white focus-within:border-teletalk-green focus-within:ring-1 focus-within:ring-teletalk-green transition shadow-sm h-10 overflow-hidden">
                        <div class="pl-3 pr-2 flex items-center justify-center text-gray-400 shrink-0">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="table-filter" placeholder="Search within table..."
                            class="flex-1 min-w-0 py-1 px-2 border-none focus:border-transparent focus:ring-0 text-sm bg-transparent shadow-none outline-none text-gray-800">
                        <button type="button" onclick="startCameraFor('table-filter')"
                            class="scanner-btn-black w-10 h-10 rounded-lg shrink-0 border-none outline-none"
                            title="Scan Barcode to Filter">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 text-white mx-auto" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M 4 8 V 4 h 4" />
                                <path d="M 16 4 h 4 v 4" />
                                <path d="M 4 16 v 4 h 4" />
                                <path d="M 16 20 h 4 v -4" />
                                <line x1="5" y1="12" x2="19" y2="12" class="animate-scanner-line" stroke="white"
                                    stroke-width="3" />
                            </svg>
                        </button>
                    </div>

                    {{-- Search Button --}}
                    <button type="button"
                        onclick="document.getElementById('table-filter').dispatchEvent(new Event('input'))"
                        class="bg-teletalk-green text-white font-bold px-4 h-10 rounded-lg hover:bg-green-800 transition text-sm shrink-0 border-none outline-none shadow-sm">
                        Search
                    </button>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead class="bg-gray-100 text-gray-600 border-b">
                        <tr>
                            <th class="p-3 text-sm font-semibold">Tracking ID</th>
                            <th class="p-3 text-sm font-semibold">Subject</th>
                            <th class="p-3 text-sm font-semibold">From (Initiator)</th>
                            <th class="p-3 text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="action-table-body">
                        @foreach($departmentFiles as $file)
                            <tr class="border-b hover:bg-gray-50 table-row-item">
                                <td class="p-3 font-mono font-bold text-sm">{{ $file->tracking_id }}</td>
                                <td class="p-3 text-sm">{{ $file->subject }}</td>
                                <td class="p-3 text-sm">{{ $file->originDepartment->name }}</td>
                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button @click="forwardModalOpen = true; forwardFileId = {{ $file->id }}"
                                            class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-blue-700 shadow-sm whitespace-nowrap">Forward</button>
                                        <button onclick="openPdfScanner({{ $file->id }})"
                                            style="background: linear-gradient(135deg, #d946ef, #8b5cf6); border: none;"
                                            class="text-white px-3 py-1.5 rounded text-xs hover:opacity-90 shadow-md whitespace-nowrap font-bold transition transform hover:-translate-y-0.5">Make PDF</button>
                                        @if($file->scanned_pdf_path)
                                            <a href="{{ asset('storage/' . $file->scanned_pdf_path) }}" target="_blank"
                                                style="background: linear-gradient(135deg, #0ea5e9, #6366f1); border: none;"
                                                class="text-white px-3 py-1.5 rounded text-xs hover:opacity-90 shadow-md whitespace-nowrap font-bold transition inline-flex items-center gap-1">
                                                Show PDF
                                            </a>
                                        @endif
                                        <button @click="archiveModalOpen = true; archiveFileId = {{ $file->id }}"
                                            class="bg-teletalk-green text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-green-800 shadow-sm whitespace-nowrap">Mark as Done</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="sm:hidden space-y-4" id="action-card-body">
                @foreach($departmentFiles as $file)
                    <div class="table-row-item border border-gray-200 rounded-xl p-4 bg-gray-50 shadow-sm space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-mono font-bold text-sm text-gray-900 truncate">{{ $file->tracking_id }}</p>
                                <p class="text-sm text-gray-700 mt-0.5 leading-snug">{{ $file->subject }}</p>
                            </div>
                            <span class="shrink-0 text-xs bg-gray-200 text-gray-700 font-semibold px-2 py-1 rounded">{{ $file->originDepartment->name }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1 border-t border-gray-200">
                            <button @click="forwardModalOpen = true; forwardFileId = {{ $file->id }}"
                                class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-blue-700 shadow-sm flex-1 text-center">Forward</button>
                            <button onclick="openPdfScanner({{ $file->id }})"
                                style="background: linear-gradient(135deg, #d946ef, #8b5cf6); border: none;"
                                class="text-white px-3 py-1.5 rounded text-xs font-bold hover:opacity-90 shadow-md flex-1 text-center">Make PDF</button>
                            @if($file->scanned_pdf_path)
                                <a href="{{ asset('storage/' . $file->scanned_pdf_path) }}" target="_blank"
                                    style="background: linear-gradient(135deg, #0ea5e9, #6366f1); border: none;"
                                    class="text-white px-3 py-1.5 rounded text-xs font-bold hover:opacity-90 shadow-md flex-1 text-center">Show PDF</a>
                            @endif
                            <button @click="archiveModalOpen = true; archiveFileId = {{ $file->id }}"
                                class="bg-teletalk-green text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-green-800 shadow-sm flex-1 text-center">Mark as Done</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==========================================
             ARCHIVE MODAL
        ========================================== --}}
        <div x-show="archiveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <div x-show="archiveModalOpen" x-transition.opacity
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="archiveModalOpen" x-transition.scale.origin.bottom
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/dak/' + archiveFileId + '/archive'" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Archive File</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">Please input the physical storage location (e.g.,
                                    Cabinet 4, Shelf B) so this file can be retrieved in future audits.</p>
                                <input type="text" name="physical_location" required placeholder="Physical Location"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                            <button @click="archiveModalOpen = false" type="button"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teletalk-green text-base font-medium text-white hover:bg-green-800 focus:outline-none sm:text-sm">
                                Confirm Archival
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==========================================
             FORWARD MODAL
        ========================================== --}}
        <div x-show="forwardModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <div x-show="forwardModalOpen" x-transition.opacity
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="forwardModalOpen" x-transition.scale.origin.bottom
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/dak/' + forwardFileId + '/forward'" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4">Forward File</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Department</label>
                                    <select name="target_department_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50">
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Remarks / Note
                                        (Optional)</label>
                                    <textarea name="remarks" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teletalk-green focus:ring focus:ring-teletalk-green focus:ring-opacity-50"
                                        placeholder="e.g., Please review the attached BTRC compliance forms."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                            <button @click="forwardModalOpen = false" type="button"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:text-sm">
                                Dispatch Forward
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         PDF SCANNER MODAL
    ========================================== --}}
    <div id="pdf-scanner-modal"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); align-items:center; justify-content:center; padding:12px;">
        <div
            style="background:#fff; border-radius:16px; width:100%; max-width:680px; max-height:92vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,0.4); display:flex; flex-direction:column;">
            {{-- Header --}}
            <div
                style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                <div>
                    <h3 style="font-size:1.1rem; font-weight:800; color:#1f2937; margin:0;">📄 Scan Document to PDF</h3>
                    <p style="font-size:0.78rem; color:#6b7280; margin:4px 0 0;">Capture one or more pages, then save as
                        a single PDF.</p>
                </div>
                <button onclick="closePdfScanner()"
                    style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#9ca3af;line-height:1;padding:4px 8px;"
                    title="Close">&times;</button>
            </div>

            {{-- Camera Feed --}}
            <div style="padding:16px 20px; display:flex; flex-direction:column; align-items:center; gap:12px; flex:1 1 auto; overflow-y:auto;">
                <div
                    style="width:100%; position:relative; background:#000; border-radius:10px; overflow:hidden; border:2px solid #8b5cf6;">
                    <video id="pdf-video" autoplay playsinline muted
                        style="width:100%; height:min(280px, 45vw); object-fit:cover; display:block;"></video>
                    <div id="pdf-overlay"
                        style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);">
                        <div style="text-align:center;color:#fff;">
                            <div style="font-size:2.5rem;"></div>
                            <p style="font-size:0.85rem;margin:8px 0 0;">Starting camera...</p>
                        </div>
                    </div>
                </div>
                <canvas id="pdf-canvas" style="display:none;"></canvas>
                <div id="pdf-status-bar"
                    style="width:100%; background:#f3f4f6; border-radius:8px; padding:10px 14px; font-size:0.82rem; color:#374151; font-weight:600; word-break:break-word;">
                    📸 Camera initializing...</div>

                {{-- Captured pages thumbnails --}}
                <div id="pdf-pages-container" style="width:100%; display:none;">
                    <p style="font-size:0.85rem; font-weight:700; color:#374151; margin:0 0 8px;">Captured Pages:</p>
                    <div id="pdf-pages-grid" style="display:flex; flex-wrap:wrap; gap:10px;"></div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div
                style="padding:14px 20px 18px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; flex-shrink:0;">
                <div style="display:flex; gap:8px; flex-wrap:wrap; flex:1 1 auto;">
                    <button onclick="capturePageForPdf()"
                        style="background:linear-gradient(135deg,#d946ef,#8b5cf6);color:#fff;border:none;padding:9px 16px;border-radius:8px;font-weight:700;cursor:pointer;font-size:0.85rem;display:flex;align-items:center;gap:6px;flex:1 1 auto;justify-content:center;white-space:nowrap;">📸
                        Capture Page</button>
                    <button onclick="savePdfAndUpload()" id="pdf-save-btn"
                        style="background:#16a34a;color:#fff;border:none;padding:9px 16px;border-radius:8px;font-weight:700;cursor:pointer;font-size:0.85rem;display:none;align-items:center;gap:6px;flex:1 1 auto;justify-content:center;white-space:nowrap;">💾
                        Save PDF</button>
                    <button onclick="clearCapturedPages()" id="pdf-clear-btn"
                        style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:9px 14px;border-radius:8px;font-weight:600;cursor:pointer;font-size:0.85rem;display:none;white-space:nowrap;">🗑
                        Clear</button>
                </div>
                <button onclick="closePdfScanner()"
                    style="background:#fff;color:#6b7280;border:1px solid #d1d5db;padding:9px 16px;border-radius:8px;font-weight:600;cursor:pointer;font-size:0.85rem;white-space:nowrap;">Cancel</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // ==========================================
        // 0. PDF Scanner — Full Rewrite
        // ==========================================
        let pdfStream = null;
        let capturedPages = [];  // Array of base64 image data URLs
        let currentPdfFileId = null;

        function openPdfScanner(fileId) {
            currentPdfFileId = fileId;
            capturedPages = [];
            renderPageThumbnails();
            const modal = document.getElementById('pdf-scanner-modal');
            modal.style.display = 'flex';
            startScanCamera();
        }

        function closePdfScanner() {
            stopScanCamera();
            const modal = document.getElementById('pdf-scanner-modal');
            modal.style.display = 'none';
            capturedPages = [];
            renderPageThumbnails();
        }

        function startScanCamera() {
            const video = document.getElementById('pdf-video');
            const overlay = document.getElementById('pdf-overlay');
            const statusBar = document.getElementById('pdf-status-bar');
            statusBar.textContent = '📷 Requesting camera access...';
            navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
                .then(stream => {
                    pdfStream = stream;
                    video.srcObject = stream;
                    video.play();
                    // Hide overlay once stream is running
                    video.onplaying = () => {
                        overlay.style.display = 'none';
                        statusBar.textContent = '✅ Camera ready! Aim at the document and click Capture Page.';
                    };
                })
                .catch(err => {
                    console.error('Camera error:', err);
                    overlay.innerHTML = '<div style="text-align:center;color:#fca5a5;"><div style="font-size:2rem;">⚠️</div><p style="margin:8px 0 0;font-size:0.9rem;">Camera error:<br>' + err.message + '</p></div>';
                    statusBar.textContent = '❌ Camera error: ' + err.message;
                });
        }

        function stopScanCamera() {
            if (pdfStream) {
                pdfStream.getTracks().forEach(t => t.stop());
                pdfStream = null;
            }
            const overlay = document.getElementById('pdf-overlay');
            if (overlay) overlay.style.display = 'flex';
            const video = document.getElementById('pdf-video');
            if (video) video.srcObject = null;
        }

        function capturePageForPdf() {
            const video = document.getElementById('pdf-video');
            const canvas = document.getElementById('pdf-canvas');
            const statusBar = document.getElementById('pdf-status-bar');

            if (!pdfStream || !video.videoWidth) {
                statusBar.textContent = '⚠️ Camera not ready yet. Please wait.';
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imgData = canvas.toDataURL('image/jpeg', 0.9);
            capturedPages.push(imgData);
            renderPageThumbnails();
            statusBar.textContent = '✅ Page ' + capturedPages.length + ' captured! Capture more or click Save PDF.';
        }

        function clearCapturedPages() {
            capturedPages = [];
            renderPageThumbnails();
            document.getElementById('pdf-status-bar').textContent = '📷 All pages cleared. Aim at document and capture again.';
        }

        function renderPageThumbnails() {
            const container = document.getElementById('pdf-pages-container');
            const grid = document.getElementById('pdf-pages-grid');
            const saveBtn = document.getElementById('pdf-save-btn');
            const clearBtn = document.getElementById('pdf-clear-btn');

            if (capturedPages.length === 0) {
                container.style.display = 'none';
                saveBtn.style.display = 'none';
                clearBtn.style.display = 'none';
                return;
            }

            container.style.display = 'block';
            saveBtn.style.display = 'flex';
            clearBtn.style.display = 'block';

            grid.innerHTML = capturedPages.map((src, i) => `
                <div style="position:relative; border:2px solid #8b5cf6; border-radius:8px; overflow:hidden; cursor:pointer;" title="Page ${i + 1}">
                    <img src="${src}" style="width:80px; height:100px; object-fit:cover; display:block;">
                    <div style="position:absolute;top:0;left:0;background:rgba(139,92,246,0.85);color:#fff;font-size:0.65rem;font-weight:700;padding:2px 5px;border-radius:0 0 6px 0;">P${i + 1}</div>
                    <div onclick="removePage(${i})" style="position:absolute;top:2px;right:2px;background:rgba(239,68,68,0.9);color:#fff;border:none;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:0.7rem;font-weight:900;">&times;</div>
                </div>
            `).join('');
        }

        function removePage(index) {
            capturedPages.splice(index, 1);
            renderPageThumbnails();
            if (capturedPages.length === 0) {
                document.getElementById('pdf-status-bar').textContent = '📷 Aim at the document and click Capture Page.';
            } else {
                document.getElementById('pdf-status-bar').textContent = capturedPages.length + ' page(s) captured.';
            }
        }

        function savePdfAndUpload() {
            if (capturedPages.length === 0) {
                alert('No pages captured! Please capture at least one page.');
                return;
            }
            const statusBar = document.getElementById('pdf-status-bar');
            const saveBtn = document.getElementById('pdf-save-btn');
            saveBtn.disabled = true;
            saveBtn.textContent = '⏳ Generating PDF...';
            statusBar.textContent = '⏳ Building ' + capturedPages.length + '-page PDF...';

            setTimeout(() => {
                try {
                    const { jsPDF } = window.jspdf;

                    const firstImg = new Image();
                    firstImg.onload = function () {
                        const pw = firstImg.naturalWidth || 800;
                        const ph = firstImg.naturalHeight || 600;
                        const pdf = new jsPDF({ orientation: pw > ph ? 'l' : 'p', unit: 'px', format: [pw, ph] });

                        capturedPages.forEach((imgData, index) => {
                            if (index > 0) pdf.addPage([pw, ph], pw > ph ? 'l' : 'p');
                            pdf.addImage(imgData, 'JPEG', 0, 0, pw, ph);
                        });

                        const pdfBlob = pdf.output('blob');
                        const formData = new FormData();
                        formData.append('pdf_file', pdfBlob, 'scanned_doc_' + currentPdfFileId + '.pdf');
                        formData.append('_token', '{{ csrf_token() }}');

                        statusBar.textContent = '⬆️ Uploading PDF to server...';

                        fetch('/dak/' + currentPdfFileId + '/upload-pdf', {
                            method: 'POST',
                            body: formData,
                            headers: { 'Accept': 'application/json' }
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    statusBar.textContent = '✅ PDF uploaded successfully! Refreshing...';
                                    setTimeout(() => { closePdfScanner(); window.location.reload(); }, 1200);
                                } else {
                                    statusBar.textContent = '❌ Upload failed: ' + (data.message || 'Unknown error');
                                    saveBtn.disabled = false;
                                    saveBtn.textContent = '💾 Save PDF';
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                statusBar.textContent = '❌ Network error during upload. Please try again.';
                                saveBtn.disabled = false;
                                saveBtn.textContent = '💾 Save PDF';
                            });
                    };
                    firstImg.src = capturedPages[0];
                } catch (e) {
                    console.error(e);
                    statusBar.textContent = '❌ Error generating PDF: ' + e.message;
                    saveBtn.disabled = false;
                    saveBtn.textContent = '💾 Save PDF';
                }
            }, 100);
        }

        // ==========================================
        // 1. Table Filtering Logic — synced to both desktop table + mobile cards
        // ==========================================
        document.getElementById('table-filter').addEventListener('input', function () {
            let filterText = this.value.toLowerCase();

            // Filter desktop table rows
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

            // Filter mobile cards
            let cards = document.querySelectorAll('#action-card-body .table-row-item');
            cards.forEach(card => {
                let cardData = card.innerText.toLowerCase();
                card.style.display = cardData.includes(filterText) ? '' : 'none';
            });

            // Handle "No Results" row on desktop
            let noResultsRow = document.getElementById('no-results-row');
            if (visibleCount === 0 && rows.length > 0) {
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
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('omni-search');
            const resultsContainerWrapper = document.getElementById('search-results-container');
            const resultsContainer = document.getElementById('search-results');

            if (searchInput && resultsContainerWrapper) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        let query = searchInput.value.trim();
                        triggerSearch(query);
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
                                            ${data.file.scanned_pdf_path ? `
                                            <div class="mt-4">
                                                <a href="/storage/${data.file.scanned_pdf_path}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition shadow-md">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    View Scanned PDF
                                                </a>
                                            </div>
                                            ` : ''}
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
                        }
                        else {
                            resultsContainer.innerHTML = `
                                <div class="p-6 sm:p-8 bg-red-50 border border-red-200 rounded-xl text-red-700 text-center shadow-inner">
                                    <svg class="h-12 w-12 sm:h-16 sm:w-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="font-bold text-xl sm:text-2xl mb-2">Record Not Found</p>
                                    <p class="text-gray-600 text-base">"We couldn't find any file matching Tracking ID: <span class="text-red-600 font-mono font-bold px-2 py-1 bg-red-100 rounded">'${query}'</span>"</p>
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