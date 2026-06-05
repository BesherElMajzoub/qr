<!DOCTYPE html>
<html lang="ar" dir="rtl" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام مسح QR للزوار</title>
    <!-- Google Fonts: Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        /* Delete row fade out */
        .fade-out {
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.6s ease;
        }

        /* Print styling rules */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .glass-card {
                background: transparent !important;
                backdrop-filter: none !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            .print-only {
                display: block !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #ddd !important;
                color: black !important;
                padding: 8px !important;
            }
            th {
                background-color: #f2f2f2 !important;
            }
            
            /* Hide specific columns for PDF print */
            .col-email, .col-scanned-at {
                display: none !important;
            }
            
            /* Allow notes to wrap fully in print */
            .visitor-notes span {
                max-width: none !important;
                display: inline !important;
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col">
    <!-- Header -->
    <header class="glass-card sticky top-0 z-40 border-b border-white/5 py-4 px-6 no-print">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.875 15.75a1.125 1.125 0 0 1-1.125-1.125v-1.5a1.125 1.125 0 0 1 1.125-1.125h1.5a1.125 1.125 0 0 1 1.125 1.125v1.5a1.125 1.125 0 0 1-1.125 1.125h-1.5ZM13.5 18.75a1.125 1.125 0 0 1 1.125-1.125h1.5a1.125 1.125 0 0 1 1.125 1.125v1.5a1.125 1.125 0 0 1-1.125 1.125h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5ZM19.5 18.75a1.125 1.125 0 0 1 1.125-1.125h.75a1.125 1.125 0 0 1 1.125 1.125v.75a1.125 1.125 0 0 1-1.125 1.125h-.75a1.125 1.125 0 0 1-1.125-1.125v-.75ZM13.5 13.5a1.125 1.125 0 0 1 1.125-1.125h.75a1.125 1.125 0 0 1 1.125 1.125v.75a1.125 1.125 0 0 1-1.125 1.125h-.75a1.125 1.125 0 0 1-1.125-1.125v-.75Z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">لوحة الزوار والمعرض</h1>
                    <p class="text-xs text-indigo-300">معرض أثاثي - لوحة المراقبة والإحصاء</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="/visitors/export" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-medium text-sm rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-500/10 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>تصدير Excel</span>
                </a>

                <button onclick="window.print()" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-medium text-sm rounded-xl transition flex items-center gap-2 shadow-lg shadow-indigo-500/10 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a3.75 3.75 0 0 1 10.56 0m-10.56 0h10.56M21 10.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm-9-6h.008v.008H12V4.5Zm0 2.25h.008v.008H12V6.75Z" />
                    </svg>
                    <span>تقرير PDF</span>
                </button>
                
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="py-2.5 px-4 bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 hover:border-white/20 font-medium text-sm rounded-xl transition flex items-center gap-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        <span class="hidden sm:inline">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- PRINT ONLY HEADER -->
    <div class="print-only max-w-7xl mx-auto w-full p-6 text-black">
        <div class="text-center mb-8 border-b-2 border-slate-900 pb-4">
            <h1 class="text-2xl font-bold">تقرير زوار معرض أثاثي 2026</h1>
            <p class="text-sm text-slate-600 mt-1">تاريخ طباعة التقرير: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 md:p-6 lg:p-8 flex flex-col gap-6">
        
        <!-- Stats Mini Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 no-print">
            <div class="glass-card rounded-3xl p-6 flex items-center justify-between shadow-xl">
                <div>
                    <span class="text-xs font-medium text-slate-400">إجمالي المسوحات والزوار</span>
                    <span class="text-3xl font-bold text-white mt-1 block" id="stat-total">{{ $stats['total'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 18M15 19.128a11.386 11.386 0 0 1-4.911-1.128m0 0A11.386 11.386 0 0 0 5.088 18M10.089 18H10M10 18H5.088m0 0a9.38 9.38 0 0 1-2.625.372 9.337 9.337 0 0 1-4.121-.952 4.125 4.125 0 0 1 7.533-2.493M10 18v-.003c0-1.113.285-2.16.786-3.07M10 18v.109A11.386 11.386 0 0 0 15 18M10 18a11.386 11.386 0 0 0 4.911-1.128m0 0A11.386 11.386 0 0 1 15 18M15 18H10" />
                    </svg>
                </div>
            </div>
            <div class="glass-card rounded-3xl p-6 flex items-center justify-between shadow-xl">
                <div>
                    <span class="text-xs font-medium text-slate-400">مسوحات اليوم الحالية</span>
                    <span class="text-3xl font-bold text-indigo-400 mt-1 block" id="stat-today">{{ $stats['today'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <div class="glass-card rounded-3xl p-6 flex items-center justify-between shadow-xl">
                <div>
                    <span class="text-xs font-medium text-slate-400">زوار فريدين (غير مكررين)</span>
                    <span class="text-3xl font-bold text-emerald-400 mt-1 block" id="stat-unique">{{ $stats['unique'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Banner Action to Go to Scanner -->
        <div class="glass-card rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl border border-indigo-500/20 bg-gradient-to-r from-indigo-950/25 to-slate-900/10 no-print">
            <div class="flex items-center gap-4 text-center md:text-right flex-col md:flex-row">
                <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center animate-pulse shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-md font-bold text-white">قارئ الأكواد مسح وتسجيل الزوار</h3>
                    <p class="text-xs text-slate-400 mt-1">افتح واجهة الكاميرا المخصصة للموبايل وابدأ بمسح الأكواد والبطاقات لتسجيل الحضور مع إمكانية إضافة ملاحظات فورية.</p>
                </div>
            </div>
            <a href="/scanner" class="py-3 px-6 bg-indigo-500 hover:bg-indigo-600 active:scale-95 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-indigo-500/10 whitespace-nowrap cursor-pointer">
                الذهاب لصفحة المسح الكاميرا &larr;
            </a>
        </div>

        <!-- Table Card -->
        <div class="glass-card rounded-3xl p-6 shadow-xl flex flex-col flex-grow">
            
            <!-- Table Header Control -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 no-print">
                <div>
                    <h2 class="text-lg font-bold text-white">سجل حضور الزوار الكلي</h2>
                    <p class="text-xs text-slate-400 mt-0.5">يمكنك البحث والتصفية أو حذف السجلات المكررة والغير مرغوبة</p>
                </div>
                
                <!-- Client-side Search -->
                <div class="relative w-full sm:max-w-xs">
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="بحث عن زائر..." 
                        class="w-full pr-10 pl-3 py-2 bg-slate-950/70 border border-white/10 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-400"
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
                            <th scope="col" class="px-4 py-3 col-email">البريد الإلكتروني</th>
                            <th scope="col" class="px-4 py-3">الهاتف</th>
                            <th scope="col" class="px-4 py-3">الملاحظات</th>
                            <th scope="col" class="px-4 py-3 col-scanned-at">وقت المسح</th>
                            <th scope="col" class="px-4 py-3 text-center rounded-l-xl no-print">خيارات التحكم</th>
                        </tr>
                    </thead>
                    <tbody id="visitors-table-body" class="divide-y divide-white/5">
                        @forelse($visitors as $visitor)
                            <tr id="visitor-row-{{ $visitor->id }}" data-raw="{{ $visitor->qr_raw_data }}" class="hover:bg-white/5 transition duration-150">
                                <td class="px-4 py-3.5 font-medium text-white visitor-name">
                                    {{ $visitor->name ?? 'غير محدد' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 visitor-email col-email">
                                    {{ $visitor->email ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 visitor-phone font-mono text-xs">
                                    {{ $visitor->phone ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 visitor-notes">
                                    <span class="max-w-[200px] inline-block truncate" title="{{ $visitor->notes }}">
                                        {{ $visitor->notes ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-400 text-xs visitor-time col-scanned-at">
                                    {{ $visitor->scanned_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3.5 text-center flex items-center justify-center gap-2 no-print">
                                    <button 
                                        onclick="copyToClipboard('{{ addslashes($visitor->qr_raw_data) }}', this)" 
                                        class="py-1.5 px-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/20 hover:border-indigo-500/30 text-xs rounded-lg transition flex items-center gap-1 cursor-pointer"
                                        title="نسخ البيانات"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m10.5 8.25V5.25m0 9v9m0-9a3.75 3.75 0 1 1-7.5 0M4.5 16.5h15" />
                                        </svg>
                                        <span>نسخ</span>
                                    </button>

                                    <button 
                                        onclick="deleteVisitor({{ $visitor->id }}, '{{ addslashes($visitor->name ?? 'غير محدد') }}')" 
                                        class="py-1.5 px-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 hover:border-red-500/30 text-xs rounded-lg transition flex items-center gap-1 cursor-pointer"
                                        title="حذف الزائر"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span>حذف</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row">
                                <td colspan="6" class="px-4 py-16 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3 text-slate-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 19.462A9 9 0 1 1 20.184 12" />
                                        </svg>
                                        <p class="text-sm">لا يوجد زوار مسجلين حتى الآن</p>
                                        <p class="text-xs text-slate-600 mt-1">اذهب لصفحة مسح الأكواد QR لتسجيل حضور زوار جدد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Alert Toaster -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition duration-500 pointer-events-none no-print">
        <div id="toast-card" class="glass-card flex items-center gap-2.5 px-4 py-3.5 rounded-2xl shadow-xl border border-white/10 max-w-sm">
            <span id="toast-icon"></span>
            <span id="toast-msg" class="text-xs font-bold text-slate-200"></span>
        </div>
    </div>

    <!-- Client-side filter search -->
    <script>
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#visitors-table-body tr:not(#empty-state-row)');
            
            rows.forEach(row => {
                const name = row.querySelector('.visitor-name').textContent.toLowerCase();
                const email = row.querySelector('.visitor-email').textContent.toLowerCase();
                const phone = row.querySelector('.visitor-phone').textContent.toLowerCase();
                const notes = row.querySelector('.visitor-notes').textContent.toLowerCase();
                const raw = row.getAttribute('data-raw').toLowerCase();

                if (name.includes(query) || email.includes(query) || phone.includes(query) || notes.includes(query) || raw.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

    <!-- AJAX Deletion Controller -->
    <script>
        function deleteVisitor(id, name) {
            if (!confirm(`هل أنت متأكد من رغبتك في حذف بيانات الزائر "${name}" نهائياً؟`)) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/visitors/${id}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById(`visitor-row-${id}`);
                    if (row) {
                        row.classList.add('fade-out');
                        setTimeout(() => {
                            row.remove();
                            // Check if table is empty
                            const remainingRows = document.querySelectorAll('#visitors-table-body tr:not(#empty-state-row)');
                            if (remainingRows.length === 0) {
                                showEmptyStatePlaceholder();
                            }
                        }, 600);
                    }
                    decrementStats();
                    showToast('success', 'تم حذف بيانات الزائر بنجاح.');
                } else {
                    showToast('error', 'فشل في حذف بيانات الزائر.');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', 'خطأ في الاتصال بالشبكة.');
            });
        }

        function decrementStats() {
            const totalEl = document.getElementById('stat-total');
            const todayEl = document.getElementById('stat-today');
            const uniqueEl = document.getElementById('stat-unique');

            if (totalEl) {
                const val = parseInt(totalEl.textContent) - 1;
                totalEl.textContent = val >= 0 ? val : 0;
            }
            if (todayEl) {
                // If scanned today, decrement (this is a simplified check, but matches total decrement locally)
                const val = parseInt(todayEl.textContent) - 1;
                todayEl.textContent = val >= 0 ? val : 0;
            }
            if (uniqueEl) {
                const val = parseInt(uniqueEl.textContent) - 1;
                uniqueEl.textContent = val >= 0 ? val : 0;
            }
        }

        function showEmptyStatePlaceholder() {
            const tableBody = document.getElementById('visitors-table-body');
            tableBody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="6" class="px-4 py-16 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3 text-slate-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 19.462A9 9 0 1 1 20.184 12" />
                            </svg>
                            <p class="text-sm">لا يوجد زوار مسجلين حتى الآن</p>
                            <p class="text-xs text-slate-600 mt-1">اذهب لصفحة مسح الأكواد QR لتسجيل حضور زوار جدد</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Copy details helper
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

        // Toaster Toast Helper
        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastCard = document.getElementById('toast-card');
            const toastIcon = document.getElementById('toast-icon');
            const toastMsg = document.getElementById('toast-msg');

            toastMsg.textContent = message;
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

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3500);
        }
    </script>
</body>
</html>
