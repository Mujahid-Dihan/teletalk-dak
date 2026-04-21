<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/teletalk-logo.png') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <script src="https://unpkg.com/html5-qrcode"></script>

        <style>
            @keyframes scanLineMove {
                0% { transform: translateY(-10px); opacity: 0.3; }
                50% { opacity: 1; }
                100% { transform: translateY(10px); opacity: 0.3; }
            }
            .animate-scanner-line {
                animation: scanLineMove 1.5s infinite alternate ease-in-out;
                filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.8));
            }
            .scanner-btn-black {
                background-color: #000000 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: #ffffff !important;
                transition: all 0.2s ease-in-out;
            }
            .scanner-btn-black:hover {
                background-color: #1a1a1a !important;
                transform: scale(1.05);
            }
        </style>
    </head>
    <body class="font-sans antialiased selection:bg-teletalk-green selection:text-white">
        <div class="min-h-screen bg-gradient-to-br from-white via-green-50 to-green-100">

            @include('layouts.navigation')


            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global QR Scanner Modal -->
        <div id="reader-container" class="hidden fixed inset-0 z-[100] bg-black bg-opacity-90 flex flex-col items-center justify-center p-4">
            <div id="reader" class="w-full max-w-md bg-white rounded-2xl overflow-hidden shadow-2xl border-4 border-white/20"></div>
            <button id="stop-scan-btn" type="button" onclick="stopScanner()" class="mt-8 bg-red-600 text-white px-10 py-3 rounded-full font-bold shadow-lg hover:bg-red-700 transition transform hover:scale-105 active:scale-95">
                Close Scanner
            </button>
        </div>

        <script>
            let html5QrCode;
            let targetInputBox = ''; 

            function startCameraFor(inputId) {
                targetInputBox = inputId;
                const container = document.getElementById('reader-container');
                container.classList.remove('hidden');
                
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }
                
                const config = { fps: 20, qrbox: { width: 280, height: 280 } };

                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        let inputElement = document.getElementById(targetInputBox);
                        if(inputElement) {
                            inputElement.value = decodedText;
                            
                            // Trigger input/change events for reactivity
                            inputElement.dispatchEvent(new Event('input', { bubbles: true }));
                            inputElement.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            // Auto-submit if part of a form
                            const form = inputElement.closest('form');
                            if (form) {
                                form.submit();
                            } else if (targetInputBox === 'omni-search' && typeof window.triggerSearch === 'function') {
                                // Auto-search if it's an omni-search field
                                window.triggerSearch(decodedText);
                            } else {
                                // Fallback: Trigger 'Enter' keypress for other cases
                                inputElement.dispatchEvent(new KeyboardEvent('keypress', { 
                                    key: 'Enter', 
                                    code: 'Enter', 
                                    keyCode: 13, 
                                    which: 13, 
                                    bubbles: true 
                                }));
                            }
                        }
                        stopScanner();
                    }
                ).catch((err) => {
                    console.error("Scanner Error:", err);
                    stopScanner();
                    alert("Unable to access camera. Please ensure permissions are granted.");
                });
            }

            function stopScanner() {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        document.getElementById('reader-container').classList.add('hidden');
                        html5QrCode.clear();
                    }).catch(err => {
                        console.warn("Cleanup error:", err);
                        document.getElementById('reader-container').classList.add('hidden');
                    });
                } else {
                    document.getElementById('reader-container').classList.add('hidden');
                }
            }
        </script>
    </body>
</html>
