<!DOCTYPE html>
<html lang="ar" dir="rtl" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قارئ الأكواد - معرض أثاثي</title>
    <!-- Google Fonts: Tajawal -->
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
            background: linear-gradient(135deg, #090d16 0%, #11102e 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
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
<body class="min-h-screen text-slate-100 flex flex-col">
    <!-- Header / Nav -->
    <header class="glass-card sticky top-0 z-40 border-b border-white/5 py-4 px-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-md font-bold text-white leading-tight">ماسح الأكواد QR</h1>
                    <p class="text-[10px] text-indigo-300">قم بتوجيه الكاميرا نحو كود الزائر للمسح</p>
                </div>
            </div>
            
            <a href="/dashboard" class="py-2 px-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer">
                <span>لوحة التحكم</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-lg w-full mx-auto p-4 flex flex-col gap-6 justify-center">
        <!-- Scanner Card -->
        <div class="glass-card rounded-3xl p-5 shadow-2xl flex flex-col relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2 text-sm font-bold text-white">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500" id="scanner-indicator"></span>
                    </span>
                    <span>الكاميرا النشطة</span>
                </div>
                
                <select id="camera-select" class="bg-slate-950 border border-white/10 rounded-xl px-2.5 py-1 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-400 max-w-[170px]">
                    <option value="">كشف الكاميرات...</option>
                </select>
            </div>

            <!-- Viewport Screen -->
            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-950 border border-white/5 flex flex-col items-center justify-center">
                
                <!-- Laser line -->
                <div id="laser-line" class="scanner-laser hidden"></div>

                <!-- Camera Stopped Placeholder -->
                <div id="scanner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 z-10 p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-3 text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-300">الكاميرا متوقفة</p>
                    <p class="text-[10px] text-slate-500 mt-1.5">اضغط على الزر أدناه لتشغيل الكاميرا ومسح أكواد الزوار</p>
                </div>

                <div id="reader" class="w-full h-full"></div>
            </div>

            <!-- Start / Stop Action Button -->
            <button id="toggle-scan-btn" class="mt-4 w-full py-3 px-4 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-2xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
                <span>تشغيل الكاميرا للبدء</span>
            </button>
        </div>

        <!-- Recent Scans History -->
        <div class="glass-card rounded-3xl p-5 shadow-lg">
            <h3 class="text-sm font-bold text-white mb-3">آخر المسوحات في هذه الجلسة</h3>
            <div id="recent-list" class="space-y-2.5">
                @forelse($recentVisitors as $recent)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5">
                        <div>
                            <div class="text-xs font-semibold text-white">{{ $recent->name ?? 'زائر غير معرف' }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $recent->scanned_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($recent->notes)
                                <span class="py-0.5 px-1.5 bg-indigo-500/20 text-indigo-300 text-[9px] rounded-md border border-indigo-500/10">ملاحظة</span>
                            @endif
                            <span class="text-[10px] text-slate-500 font-mono">{{ $recent->phone ?? '-' }}</span>
                        </div>
                    </div>
                @empty
                    <div id="no-scans-text" class="text-center py-6 text-xs text-slate-500">لا توجد عمليات مسح مؤخراً</div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- CONFIRMATION / NOTES MODAL (Glassmorphic Overlay) -->
    <div id="confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md hidden transition duration-300">
        <div class="glass-card w-full max-w-md rounded-3xl p-6 shadow-2xl relative animate-fade-in">
            <h3 class="text-lg font-bold text-white text-center mb-1">مراجعة بيانات الزائر</h3>
            <p class="text-xs text-slate-400 text-center mb-5">يمكنك مراجعة وتعديل بيانات الزائر وإضافة ملاحظات قبل التأكيد</p>

            <form id="confirm-scan-form" class="space-y-4">
                <input type="hidden" id="modal-qr-raw" name="qr_raw_data">
                
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">اسم الزائر</label>
                    <input type="text" id="modal-name" name="name" class="w-full bg-slate-950/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-600">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">البريد الإلكتروني</label>
                        <input type="email" id="modal-email" name="email" class="w-full bg-slate-950/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">الهاتف</label>
                        <input type="text" id="modal-phone" name="phone" class="w-full bg-slate-950/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-600 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">ملاحظات إضافية (اختياري)</label>
                    <textarea id="modal-notes" name="notes" rows="3" placeholder="أدخل أي ملاحظات ترغب في مراجعتها لاحقاً..." class="w-full bg-slate-950/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-600 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" id="modal-submit-btn" class="flex-grow py-3 bg-emerald-600 hover:bg-emerald-500 active:scale-98 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-600/10 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>تأكيد وحفظ الزائر</span>
                    </button>
                    <button type="button" onclick="cancelScan()" class="py-3 px-5 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 rounded-xl text-sm font-semibold transition cursor-pointer">
                        <span>إلغاء</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Toaster -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition duration-500 pointer-events-none">
        <div id="toast-card" class="glass-card flex items-center gap-2.5 px-4 py-3.5 rounded-2xl shadow-xl border border-white/10 max-w-sm">
            <span id="toast-icon"></span>
            <span id="toast-msg" class="text-xs font-bold text-slate-200"></span>
        </div>
    </div>

    <!-- Sound Audio setup -->
    <script>
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

        function playSuccessSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
            gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
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
            osc.frequency.setValueAtTime(220, audioCtx.currentTime); // A3 note
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.4);
        }
    </script>

    <!-- html5-qrcode scanner controller logic -->
    <script>
        let html5QrcodeScanner = null;
        let isScanning = false;

        const toggleBtn = document.getElementById('toggle-scan-btn');
        const cameraSelect = document.getElementById('camera-select');
        const laserLine = document.getElementById('laser-line');
        const scannerPlaceholder = document.getElementById('scanner-placeholder');
        const scannerIndicator = document.getElementById('scanner-indicator');

        // Modal elements
        const confirmModal = document.getElementById('confirm-modal');
        const modalQrRaw = document.getElementById('modal-qr-raw');
        const modalName = document.getElementById('modal-name');
        const modalEmail = document.getElementById('modal-email');
        const modalPhone = document.getElementById('modal-phone');
        const modalNotes = document.getElementById('modal-notes');
        const confirmForm = document.getElementById('confirm-scan-form');
        const submitBtn = document.getElementById('modal-submit-btn');

        toggleBtn.addEventListener('click', () => {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        });

        // Initialize Cameras list
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                cameraSelect.innerHTML = '';
                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    let label = device.label || `كاميرا ${index + 1}`;
                    if (label.toLowerCase().includes('back') || label.toLowerCase().includes('environment')) {
                        label += ' (الكاميرا الخلفية)';
                    } else if (label.toLowerCase().includes('front') || label.toLowerCase().includes('user')) {
                        label += ' (الكاميرا الأمامية)';
                    }
                    option.textContent = label;
                    cameraSelect.appendChild(option);
                });

                // Focus back camera by default
                const backCam = devices.find(d => 
                    d.label.toLowerCase().includes('back') || 
                    d.label.toLowerCase().includes('environment')
                );
                if (backCam) {
                    cameraSelect.value = backCam.id;
                }
            } else {
                cameraSelect.innerHTML = '<option value="">لا توجد كاميرات</option>';
            }
        }).catch(err => {
            console.error(err);
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

            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5Qrcode("reader");
            }

            scannerPlaceholder.classList.add('hidden');
            laserLine.classList.remove('hidden');

            html5QrcodeScanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: function(width, height) {
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
                console.error(err);
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
                    console.error(err);
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
                <span>تشغيل الكاميرا للبدء</span>
            `;
            toggleBtn.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
            toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-500');
            scannerIndicator.classList.remove('bg-green-500');
            scannerIndicator.classList.add('bg-red-500');
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Pause scanner
            html5QrcodeScanner.pause(true);
            laserLine.classList.add('hidden');

            // Prefill modal fields
            modalQrRaw.value = decodedText;
            modalName.value = '';
            modalEmail.value = '';
            modalPhone.value = '';
            modalNotes.value = '';

            try {
                const data = JSON.parse(decodedText);
                if (typeof data === 'object' && data !== null) {
                    modalName.value = data.name || '';
                    modalEmail.value = data.email || '';
                    modalPhone.value = data.phone || '';
                }
            } catch (e) {
                // Not JSON, leave prefilled fields blank (user can input manually if desired)
            }

            // Open Modal
            confirmModal.classList.remove('hidden');
        }

        function onScanFailure(error) {
            // Ignore scan failure ticks
        }

        // Handle Modal Form Submission
        confirmForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable submit button during processing
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>جاري الحفظ...</span>
            `;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("/visitors/scan", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    qr_raw_data: modalQrRaw.value,
                    name: modalName.value,
                    email: modalEmail.value,
                    phone: modalPhone.value,
                    notes: modalNotes.value
                })
            })
            .then(res => res.json())
            .then(data => {
                confirmModal.classList.add('hidden');
                
                if (data.status === 'success') {
                    playSuccessSound();
                    showToast('success', 'تم حفظ بيانات الزائر بنجاح!');
                    prependToRecentList(data.visitor);
                } else if (data.status === 'duplicate') {
                    playWarningSound();
                    showToast('duplicate', 'هذا الكود مسجل مسبقاً في النظام!');
                } else {
                    playWarningSound();
                    showToast('error', 'حدث خطأ أثناء حفظ البيانات.');
                }
            })
            .catch(err => {
                console.error(err);
                confirmModal.classList.add('hidden');
                playWarningSound();
                showToast('error', 'خطأ في الاتصال بالشبكة.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                // Resume camera
                if (isScanning && html5QrcodeScanner) {
                    html5QrcodeScanner.resume();
                    laserLine.classList.remove('hidden');
                }
            });
        });

        function cancelScan() {
            confirmModal.classList.add('hidden');
            if (isScanning && html5QrcodeScanner) {
                html5QrcodeScanner.resume();
                laserLine.classList.remove('hidden');
            }
        }

        // Toaster Toast Helper
        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastCard = document.getElementById('toast-card');
            const toastIcon = document.getElementById('toast-icon');
            const toastMsg = document.getElementById('toast-msg');

            toastMsg.textContent = message;

            // Reset classes
            toastCard.className = 'glass-card flex items-center gap-2.5 px-4 py-3.5 rounded-2xl shadow-xl border max-w-sm';

            if (type === 'success') {
                toastCard.classList.add('border-green-500/20', 'bg-green-500/5');
                toastIcon.innerHTML = `
                    <div class="w-7 h-7 rounded-full bg-green-500/20 border border-green-500/30 flex items-center justify-center text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                `;
            } else if (type === 'duplicate') {
                toastCard.classList.add('border-yellow-500/20', 'bg-yellow-500/5');
                toastIcon.innerHTML = `
                    <div class="w-7 h-7 rounded-full bg-yellow-500/20 border border-yellow-500/30 flex items-center justify-center text-yellow-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                `;
            } else {
                toastCard.classList.add('border-red-500/20', 'bg-red-500/5');
                toastIcon.innerHTML = `
                    <div class="w-7 h-7 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                `;
            }

            // Slide up & show
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3500);
        }

        // Add dynamically saved visitor to the bottom compact list
        function prependToRecentList(visitor) {
            const list = document.getElementById('recent-list');
            const noScans = document.getElementById('no-scans-text');
            if (noScans) {
                noScans.remove();
            }

            const card = document.createElement('div');
            card.className = 'flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5 animate-fade-in';
            
            let notesTag = '';
            if (visitor.notes && visitor.notes.trim() !== '') {
                notesTag = `<span class="py-0.5 px-1.5 bg-indigo-500/20 text-indigo-300 text-[9px] rounded-md border border-indigo-500/10">ملاحظة</span>`;
            }

            card.innerHTML = `
                <div>
                    <div class="text-xs font-semibold text-white">${visitor.name || 'زائر غير معرف'}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">الآن</div>
                </div>
                <div class="flex items-center gap-2">
                    ${notesTag}
                    <span class="text-[10px] text-slate-500 font-mono">${visitor.phone || '-'}</span>
                </div>
            `;

            list.insertBefore(card, list.firstChild);

            // Limit recent elements shown in scanner list to 5
            if (list.children.length > 5) {
                list.removeChild(list.lastChild);
            }
        }
    </script>
</body>
</html>
