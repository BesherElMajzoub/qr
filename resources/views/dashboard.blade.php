<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام مسح QR للزوار</title>
    <!-- Google Fonts: Tajawal for Arabic, Inter for English code -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- html5-qrcode library from CDN -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }

        /* Micro-animations and effects */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px border;
            border-color: rgba(255, 255, 255, 0.08);
        }

        @keyframes scan-line {
            0% { top: 0%; opacity: 0.3; }
            50% { opacity: 1; }
            100% { top: 100%; opacity: 0.3; }
        }

        .scanner-laser {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, rgba(239, 68, 68, 0) 0%, rgba(239, 68, 68, 1) 50%, rgba(239, 68, 68, 0) 100%);
            animation: scan-line 2s linear infinite;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.8);
            z-index: 10;
        }

        /* New row flash animation */
        @keyframes flash-green {
            0% { background-color: rgba(34, 197, 94, 0.3); }
            100% { background-color: transparent; }
        }
        .row-new {
            animation: flash-green 2s ease-out;
        }

        /* Duplicate row flash animation */
        @keyframes flash-yellow {
            0% { background-color: rgba(234, 179, 8, 0.3); }
            100% { background-color: transparent; }
        }
        .row-duplicate {
            animation: flash-yellow 3s ease-out;
        }

        /* html5-qrcode scanner layout overrides to make it look premium */
        #reader {
            border: none !important;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #020617 !important;
        }
        #reader video {
            width: 100% !important;
            border-radius: 1.5rem;
            object-fit: cover !important;
        }
    </style>
</head>
<body class="h-full text-slate-100 flex flex-col">
    <!-- Header -->
    <header class="glass-card sticky top-0 z-50 border-b border-white/5 py-4 px-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-lg shadow-indigo-500/5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.875 15.75a1.125 1.125 0 0 1-1.125-1.125v-1.5a1.125 1.125 0 0 1 1.125-1.125h1.5a1.125 1.125 0 0 1 1.125 1.125v1.5a1.125 1.125 0 0 1-1.125 1.125h-1.5ZM13.5 18.75a1.125 1.125 0 0 1 1.125-1.125h1.5a1.125 1.125 0 0 1 1.125 1.125v1.5a1.125 1.125 0 0 1-1.125 1.125h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5ZM19.5 18.75a1.125 1.125 0 0 1 1.125-1.125h.75a1.125 1.125 0 0 1 1.125 1.125v.75a1.125 1.125 0 0 1-1.125 1.125h-.75a1.125 1.125 0 0 1-1.125-1.125v-.75ZM13.5 13.5a1.125 1.125 0 0 1 1.125-1.125h.75a1.125 1.125 0 0 1 1.125 1.125v.75a1.125 1.125 0 0 1-1.125 1.125h-.75a1.125 1.125 0 0 1-1.125-1.125v-.75Z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">لوحة الزوار والمعرض</h1>
                    <p class="text-xs text-indigo-300">أثاثي - نظام مسح وتسجيل رموز QR</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('visitors.export') }}" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-medium text-sm rounded-xl transition duration-300 flex items-center gap-2 shadow-lg shadow-emerald-500/10 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>تصدير Excel (CSV)</span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="py-2.5 px-4 bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 hover:border-white/20 font-medium text-sm rounded-xl transition duration-300 flex items-center gap-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        <span class="hidden sm:inline">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 md:p-6 lg:p-8 flex flex-col lg:flex-row gap-6 overflow-hidden">
        
        <!-- Left Side: Scanner & Controls -->
        <div class="w-full lg:w-5/12 flex flex-col gap-6">
            <!-- Camera QR Scanner Card -->
            <div class="glass-card rounded-3xl p-6 shadow-xl flex flex-col relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold flex items-center gap-2 text-white">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500" id="scanner-indicator"></span>
                        </span>
                        <span>قارئ الـ QR Code</span>
                    </h2>
                    
                    <select id="camera-select" class="bg-slate-900 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-400 max-w-[180px]">
                        <option value="">كشف الكاميرات...</option>
                    </select>
                </div>

                <!-- Scanner Screen -->
                <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-950 border border-white/5 flex flex-col items-center justify-center">
                    
                    <!-- Scanner Laser (Visible only when scanning) -->
                    <div id="laser-line" class="scanner-laser hidden"></div>

                    <!-- Overlay for pause / processing state -->
                    <div id="scanner-overlay" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center hidden transition duration-300">
                        <!-- Spinner or Alert icon -->
                        <div id="overlay-icon" class="mb-3 text-indigo-400">
                            <!-- SVG will be injected here -->
                        </div>
                        <p id="overlay-message" class="text-sm font-bold text-slate-200 text-center px-4"></p>
                        <p id="overlay-countdown" class="text-xs text-indigo-300 mt-1"></p>
                    </div>

                    <!-- Camera placeholder when camera is stopped -->
                    <div id="scanner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 z-10 p-6 text-center">
                        <div class="w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-4 text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-300">الكاميرا متوقفة حالياً</p>
                        <p class="text-xs text-slate-500 mt-2">اضغط على زر تشغيل الكاميرا بالأسفل للبدء بالمسح الضوئي للزوار</p>
                    </div>

                    <!-- Reader target -->
                    <div id="reader" class="w-full h-full"></div>
                </div>

                <!-- Control Button -->
                <button id="toggle-scan-btn" class="mt-5 w-full py-3.5 px-4 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-indigo-500/20 active:scale-98 transition duration-300 flex items-center justify-center gap-2 cursor-pointer z-30">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    <span>تشغيل الكاميرا</span>
                </button>
            </div>

            <!-- Stats Mini Dashboard -->
            <div class="grid grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4 flex flex-col justify-between shadow-lg">
                    <span class="text-xs text-slate-400">إجمالي المسوح</span>
                    <span class="text-2xl font-bold text-white mt-1" id="stat-total">{{ $stats['total'] }}</span>
                </div>
                <div class="glass-card rounded-2xl p-4 flex flex-col justify-between shadow-lg">
                    <span class="text-xs text-slate-400">مسوحات اليوم</span>
                    <span class="text-2xl font-bold text-indigo-400 mt-1" id="stat-today">{{ $stats['today'] }}</span>
                </div>
                <div class="glass-card rounded-2xl p-4 flex flex-col justify-between shadow-lg">
                    <span class="text-xs text-slate-400">زوار فريدين</span>
                    <span class="text-2xl font-bold text-emerald-400 mt-1" id="stat-unique">{{ $stats['unique'] }}</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Visitors Table -->
        <div class="w-full lg:w-7/12 flex flex-col glass-card rounded-3xl p-6 shadow-xl overflow-hidden min-h-[400px]">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">قائمة زوار المعرض المسجلين</h2>
                    <p class="text-xs text-slate-400 mt-0.5">يتم التحديث فورياً عند مسح أي كود QR</p>
                </div>
                
                <!-- Client-side Search -->
                <div class="relative w-full sm:max-w-xs">
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="بحث عن زائر..." 
                        class="w-full pr-10 pl-3 py-2 bg-slate-950 border border-white/10 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="flex-grow overflow-x-auto relative min-h-[300px]">
                <table class="w-full text-right text-sm">
                    <thead class="text-xs text-slate-400 uppercase bg-white/5 rounded-xl">
                        <tr>
                            <th scope="col" class="px-4 py-3 rounded-r-xl">الاسم</th>
                            <th scope="col" class="px-4 py-3">البريد الإلكتروني</th>
                            <th scope="col" class="px-4 py-3">الهاتف</th>
                            <th scope="col" class="px-4 py-3">وقت المسح</th>
                            <th scope="col" class="px-4 py-3 text-center rounded-l-xl">بيانات الكود</th>
                        </tr>
                    </thead>
                    <tbody id="visitors-table-body" class="divide-y divide-white/5">
                        @forelse($visitors as $visitor)
                            <tr id="visitor-row-{{ $visitor->id }}" data-raw="{{ $visitor->qr_raw_data }}" class="hover:bg-white/5 transition duration-150">
                                <td class="px-4 py-3.5 font-medium text-white visitor-name">
                                    {{ $visitor->name ?? 'غير محدد' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 visitor-email">
                                    {{ $visitor->email ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 visitor-phone font-mono text-xs">
                                    {{ $visitor->phone ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-400 text-xs">
                                    {{ $visitor->scanned_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <button 
                                        onclick="copyToClipboard('{{ addslashes($visitor->qr_raw_data) }}', this)" 
                                        class="py-1 px-2.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/20 hover:border-indigo-500/30 text-xs rounded-lg transition duration-200 flex items-center gap-1 mx-auto cursor-pointer"
                                        title="نسخ البيانات الخام"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m10.5 8.25V5.25m0 9v9m0-9a3.75 3.75 0 1 1-7.5 0M4.5 16.5h15" />
                                        </svg>
                                        <span>نسخ</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row">
                                <td colspan="5" class="px-4 py-16 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3 text-slate-600 animate-pulse">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 19.462A9 9 0 1 1 20.184 12" />
                                        </svg>
                                        <p class="text-sm">لا يوجد زوار مسجلين حتى الآن</p>
                                        <p class="text-xs text-slate-600 mt-1">ابدأ بمسح كود QR لتسجيل الحضور</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Sound effects system using Web Audio API -->
    <script>
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

        function playSuccessSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            
            osc.type = 'sine';
            // High clear beep
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
            gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
            // Quick exponential decay
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
            
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            osc.start();
            osc.stop(audioCtx.currentTime + 0.25);
        }

        function playWarningSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            
            osc.type = 'triangle';
            // Low warning double buzz
            osc.frequency.setValueAtTime(220, audioCtx.currentTime); // A3 note
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
            
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            osc.start();
            osc.stop(audioCtx.currentTime + 0.4);
        }
    </script>

    <!-- Clipboard Helper -->
    <script>
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(() => {
                const originalContent = button.innerHTML;
                button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span class="text-green-400">تم!</span>
                `;
                button.classList.add('bg-green-500/10', 'border-green-500/20');
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('bg-green-500/10', 'border-green-500/20');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>

    <!-- Client-side filter -->
    <script>
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#visitors-table-body tr:not(#empty-state-row)');
            
            rows.forEach(row => {
                const name = row.querySelector('.visitor-name').textContent.toLowerCase();
                const email = row.querySelector('.visitor-email').textContent.toLowerCase();
                const phone = row.querySelector('.visitor-phone').textContent.toLowerCase();
                const raw = row.getAttribute('data-raw').toLowerCase();

                if (name.includes(query) || email.includes(query) || phone.includes(query) || raw.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

    <!-- html5-qrcode scanner logic integration -->
    <script>
        let html5QrcodeScanner = null;
        let isScanning = false;
        
        const toggleBtn = document.getElementById('toggle-scan-btn');
        const cameraSelect = document.getElementById('camera-select');
        const laserLine = document.getElementById('laser-line');
        const overlay = document.getElementById('scanner-overlay');
        const overlayIcon = document.getElementById('overlay-icon');
        const overlayMsg = document.getElementById('overlay-message');
        const overlayCountdown = document.getElementById('overlay-countdown');
        const scannerPlaceholder = document.getElementById('scanner-placeholder');
        const scannerIndicator = document.getElementById('scanner-indicator');

        // Check for audio activation
        toggleBtn.addEventListener('click', () => {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        });

        // Initialize Cameras Dropdown list
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                cameraSelect.innerHTML = '';
                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    // Attempt to show camera friendly name
                    let label = device.label || `كاميرا ${index + 1}`;
                    if (label.toLowerCase().includes('back') || label.toLowerCase().includes('environment')) {
                        label += ' (الكاميرا الخلفية)';
                    } else if (label.toLowerCase().includes('front') || label.toLowerCase().includes('user')) {
                        label += ' (الكاميرا الأمامية)';
                    }
                    option.textContent = label;
                    cameraSelect.appendChild(option);
                });
                // Default select rear camera if exists
                const backCam = devices.find(device => 
                    device.label.toLowerCase().includes('back') || 
                    device.label.toLowerCase().includes('environment')
                );
                if (backCam) {
                    cameraSelect.value = backCam.id;
                }
            } else {
                cameraSelect.innerHTML = '<option value="">لا توجد كاميرات</option>';
            }
        }).catch(err => {
            console.error('Error fetching cameras', err);
            cameraSelect.innerHTML = '<option value="">خطأ في الكشف</option>';
        });

        toggleBtn.addEventListener('click', function() {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        });

        function startScanner() {
            const cameraId = cameraSelect.value;
            if (!cameraId) {
                alert('يرجى تحديد كاميرا أولاً.');
                return;
            }

            // Create scanner instance if not exists
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5Qrcode("reader");
            }

            // Hide placeholder & Show Laser line
            scannerPlaceholder.classList.add('hidden');
            laserLine.classList.remove('hidden');
            
            // Start scanning
            html5QrcodeScanner.start(
                cameraId, 
                {
                    fps: 10,
                    qrbox: function(width, height) {
                        // Dynamic sizing
                        const min = Math.min(width, height);
                        return { width: min * 0.7, height: min * 0.7 };
                    }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                isScanning = true;
                toggleBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                    </svg>
                    <span>إيقاف الكاميرا</span>
                `;
                toggleBtn.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
                toggleBtn.classList.add('bg-red-600', 'hover:bg-red-500');
                scannerIndicator.classList.remove('bg-red-500');
                scannerIndicator.classList.add('bg-green-500');
            }).catch(err => {
                console.error("Unable to start scanner.", err);
                alert("خطأ في تشغيل الكاميرا: " + err);
                stopScanner();
            });
        }

        function stopScanner() {
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.stop().then(() => {
                    isScanning = false;
                    cleanupScannerUI();
                }).catch(err => {
                    console.error("Failed to stop scanner.", err);
                    isScanning = false;
                    cleanupScannerUI();
                });
            } else {
                cleanupScannerUI();
            }
        }

        function cleanupScannerUI() {
            laserLine.classList.add('hidden');
            scannerPlaceholder.classList.remove('hidden');
            toggleBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
                <span>تشغيل الكاميرا</span>
            `;
            toggleBtn.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
            toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-500');
            scannerIndicator.classList.remove('bg-green-500');
            scannerIndicator.classList.add('bg-red-500');
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Pause scan to process
            html5QrcodeScanner.pause(true);
            laserLine.classList.add('hidden');
            
            showOverlay('processing', 'جاري معالجة الكود...');

            // Send to database via AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch("{{ route('visitors.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ qr_raw_data: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Success scan
                    playSuccessSound();
                    showOverlay('success', 'تم التسجيل بنجاح!');
                    prependVisitorToTable(data.visitor);
                    updateStats(1, 1, 1); // increment stats
                } else if (data.status === 'duplicate') {
                    // Duplicate scan
                    playWarningSound();
                    showOverlay('duplicate', 'هذا الكود مسجل مسبقاً!');
                    highlightDuplicateRow(data.visitor.id);
                } else {
                    // Fail
                    playWarningSound();
                    showOverlay('error', 'فشل في حفظ الكود.');
                }
            })
            .catch(err => {
                console.error("AJAX Error", err);
                playWarningSound();
                showOverlay('error', 'حدث خطأ في الشبكة.');
            })
            .finally(() => {
                // Resume scanning after 2.5 seconds countdown
                startCountdown(2.5);
            });
        }

        function onScanFailure(error) {
            // Avoid console spam of QR scanning attempts
        }

        function showOverlay(type, message) {
            overlay.classList.remove('hidden');
            overlayMsg.textContent = message;
            
            // Set overlay color and icon
            if (type === 'processing') {
                overlay.style.backgroundColor = 'rgba(2, 6, 23, 0.95)';
                overlayIcon.innerHTML = `
                    <svg class="animate-spin h-10 w-10 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
            } else if (type === 'success') {
                overlay.style.backgroundColor = 'rgba(21, 128, 61, 0.9)';
                overlayIcon.innerHTML = `
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                `;
            } else if (type === 'duplicate') {
                overlay.style.backgroundColor = 'rgba(180, 83, 9, 0.9)';
                overlayIcon.innerHTML = `
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                `;
            } else {
                overlay.style.backgroundColor = 'rgba(185, 28, 28, 0.9)';
                overlayIcon.innerHTML = `
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                `;
            }
        }

        function startCountdown(seconds) {
            let remain = seconds;
            overlayCountdown.textContent = `استئناف الكاميرا خلال ${remain.toFixed(1)} ثانية...`;
            
            const timer = setInterval(() => {
                remain -= 0.1;
                if (remain <= 0) {
                    clearInterval(timer);
                    overlay.classList.add('hidden');
                    if (isScanning && html5QrcodeScanner) {
                        html5QrcodeScanner.resume();
                        laserLine.classList.remove('hidden');
                    }
                } else {
                    overlayCountdown.textContent = `استئناف الكاميرا خلال ${remain.toFixed(1)} ثانية...`;
                }
            }, 100);
        }

        function prependVisitorToTable(visitor) {
            const tableBody = document.getElementById('visitors-table-body');
            const emptyState = document.getElementById('empty-state-row');
            
            if (emptyState) {
                emptyState.remove();
            }

            const tr = document.createElement('tr');
            tr.id = `visitor-row-${visitor.id}`;
            tr.setAttribute('data-raw', visitor.qr_raw_data);
            tr.className = 'hover:bg-white/5 transition duration-150 row-new';
            
            const escapeRaw = visitor.qr_raw_data.replace(/'/g, "\\'");
            
            tr.innerHTML = `
                <td class="px-4 py-3.5 font-medium text-white visitor-name">
                    ${visitor.name || 'غير محدد'}
                </td>
                <td class="px-4 py-3.5 text-slate-300 visitor-email">
                    ${visitor.email || '-'}
                </td>
                <td class="px-4 py-3.5 text-slate-300 visitor-phone font-mono text-xs">
                    ${visitor.phone || '-'}
                </td>
                <td class="px-4 py-3.5 text-slate-400 text-xs">
                    الآن
                </td>
                <td class="px-4 py-3.5 text-center">
                    <button 
                        onclick="copyToClipboard('${escapeRaw}', this)" 
                        class="py-1 px-2.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/20 hover:border-indigo-500/30 text-xs rounded-lg transition duration-200 flex items-center gap-1 mx-auto cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m10.5 8.25V5.25m0 9v9m0-9a3.75 3.75 0 1 1-7.5 0M4.5 16.5h15" />
                        </svg>
                        <span>نسخ</span>
                    </button>
                </td>
            `;
            
            tableBody.insertBefore(tr, tableBody.firstChild);
        }

        function highlightDuplicateRow(id) {
            const row = document.getElementById(`visitor-row-${id}`);
            if (row) {
                row.classList.remove('row-duplicate');
                void row.offsetWidth; // Trigger reflow to restart CSS animation
                row.classList.add('row-duplicate');
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function updateStats(totalInc, todayInc, uniqueInc) {
            const totalEl = document.getElementById('stat-total');
            const todayEl = document.getElementById('stat-today');
            const uniqueEl = document.getElementById('stat-unique');
            
            if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + totalInc;
            if (todayEl) todayEl.textContent = parseInt(todayEl.textContent) + todayInc;
            if (uniqueEl) uniqueEl.textContent = parseInt(uniqueEl.textContent) + uniqueInc;
        }
    </script>
</body>
</html>
