<?php
$currentPage = 'layouting';
$evalConfig = ['enabled' => false];
?>
<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" name="viewport" />
    <title>Layouting - Turikale Print</title>
    <meta name="description" content="Aplikasi studio cetak foto dan layouting pas foto profesional, cepat, presisi, dan mudah digunakan." />

    <link rel="icon" type="image/jpeg" href="../assets/images/Logo-Icon-TurikalePrint.jpg" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.7/dist/bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc7fb',
                            400: '#38a9f8',
                            500: '#0e8ee9',
                            600: '#0270c7',
                            700: '#0359a1',
                            800: '#074c84',
                            900: '#0c3f6e',
                            950: '#082849',
                        },
                        studio: {
                            bg: '#f8fafc',
                            card: '#ffffff',
                            border: '#e2e8f0',
                            text: '#0f172a',
                            muted: '#64748b'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    boxShadow: {
                        'paper': '0 20px 45px -15px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.08)',
                        'floating': '0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        };
    </script>

    <style>

        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .canvas-grid-pattern {
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 16px 16px;
        }

        .dark .canvas-grid-pattern {
            background-color: #0b1120;
            background-image: radial-gradient(#334155 0.75px, transparent 0.75px), radial-gradient(#334155 0.75px, #0b1120 0.75px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        .snap-guide {
            position: absolute;
            background-color: #0284c7;
            z-index: 60;
            pointer-events: none;
        }

        .snap-guide-v {
            width: 1.5px;
            top: 0;
            bottom: 0;
        }

        .snap-guide-h {
            height: 1.5px;
            left: 0;
            right: 0;
        }

        .marquee-selection-box {
            position: absolute;
            border: 1.5px dashed #0284c7;
            background-color: rgba(2, 132, 199, 0.14);
            pointer-events: none;
            z-index: 70;
            border-radius: 2px;
        }

        .canvas-photo-item {
            position: absolute;
            user-select: none;
            touch-action: none;
            cursor: grab;
            transition: box-shadow 0.15s ease, outline 0.15s ease;
            overflow: hidden;
        }

        .canvas-photo-item:active {
            cursor: grabbing;
        }

        .canvas-photo-item.is-selected {
            outline: 2.5px solid #0284c7;
            outline-offset: 1px;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);
            z-index: 45 !important;
        }

        .cut-mark-dashed-box {
            position: absolute;
            pointer-events: none;
            border-style: dashed !important;
            border-width: 0.5px !important;
            border-color: #64748b !important;
            box-sizing: border-box !important;
            z-index: 25;
            background: transparent !important;
            transition: opacity 0.08s ease-out;
        }

        .crop-mark-corner {
            position: absolute;
            width: 5px;
            height: 5px;
            pointer-events: none;
            box-sizing: border-box !important;
        }

        .cm-tl {
            top: -1px;
            left: -1px;
            border-top: 1.5px solid #334155;
            border-left: 1.5px solid #334155;
        }

        .cm-tr {
            top: -1px;
            right: -1px;
            border-top: 1.5px solid #334155;
            border-right: 1.5px solid #334155;
        }

        .cm-bl {
            bottom: -1px;
            left: -1px;
            border-bottom: 1.5px solid #334155;
            border-left: 1.5px solid #334155;
        }

        .cm-br {
            bottom: -1px;
            right: -1px;
            border-bottom: 1.5px solid #334155;
            border-right: 1.5px solid #334155;
        }

        .cropper-container,
        .cropper-wrap-box,
        .cropper-canvas,
        .cropper-drag-box,
        .cropper-crop-box,
        .cropper-view-box {
            touch-action: none !important;
            -webkit-touch-callout: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            overscroll-behavior: contain !important;
        }

        .cropper-view-box,
        .cropper-face {
            border-radius: 2px;
        }

        .cropper-line, .cropper-point {
            background-color: #0284c7;
        }

        @keyframes pulse-confirm {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
        }

        .animate-pulse-confirm {
            animation: pulse-confirm 1.8s infinite;
        }

        #floating-photo-toolbar {
            transition: opacity 0.15s ease;
        }

        @media print {
            @page {
                margin: 0 !important;
                size: auto;
            }

            *, *::before, *::after {
                box-sizing: border-box !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                min-height: 100% !important;
                background: #ffffff !important;
                overflow: visible !important;
            }

            body * {
                visibility: hidden !important;
            }

            #paper-canvas,
            #paper-canvas * {
                visibility: visible !important;
            }

            #step-print,
            #paper-viewport,
            #paper-workspace {
                display: block !important;
                position: static !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                transform: none !important;
                width: auto !important;
                height: auto !important;
                overflow: visible !important;
                background: transparent !important;
            }

            #paper-canvas {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                transform: none !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .canvas-photo-item {
                position: absolute !important;
                outline: none !important;
                box-shadow: none !important;
                border: none !important;
            }

            .cut-mark-dashed-box {
                display: block !important;
                border-style: dashed !important;
                border-width: 0.5px !important;
                border-color: rgba(203, 213, 225, 0.6) !important;
                box-sizing: border-box !important;
                background: transparent !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .crop-mark-corner {
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            #floating-photo-toolbar,
            .snap-guide,
            .marquee-selection-box {
                display: none !important;
                visibility: hidden !important;
            }
        }

.kreasi-instan-section {
    margin-top: 40px;
    margin-bottom: 40px;
    padding: 0 15px;
    text-align: center;
}

.kreasi-title {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 20px;
    font-weight: 600;
}

.kreasi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    max-width: 900px;
    margin: 0 auto;
}

.kreasi-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 15px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.kreasi-card:hover {
    transform: translateY(-4px);
    border-color: #0d6efd;
    box-shadow: 0 6px 15px rgba(13, 110, 253, 0.1);
}

.card-icon {
    font-size: 2.2rem;
    margin-bottom: 8px;
}

.kreasi-card h4 {
    margin: 0 0 4px 0;
    font-size: 1rem;
    font-weight: 700;
}

.kreasi-card p {
    margin: 0;
    font-size: 0.8rem;
    color: #888;
}

@media (max-width: 768px) {
    .kreasi-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 12px;
        padding-bottom: 10px;

        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .kreasi-grid::-webkit-scrollbar {
        display: none;
    }

    .kreasi-card {
        flex: 0 0 65%;
        scroll-snap-align: center;
    }
}
    </style>
</head>

<body class="bg-studio-bg text-studio-text font-sans antialiased min-h-screen w-screen relative selection:bg-slate-900 selection:text-white dark:bg-slate-950 dark:text-slate-100 flex flex-col overflow-x-hidden">

<div id="preloader"
    class="fixed inset-0 z-[9999] bg-white dark:bg-slate-950 flex flex-col items-center justify-center transition-opacity duration-700">

    <div class="relative flex items-center justify-center">
        <img
            src="../assets/images/Logo-Icon-TurikalePrint.jpg"
            alt="Turikale Print"
            class="w-24 h-24 md:w-28 md:h-28 object-contain rounded-2xl animate-pulse">
    </div>

    <h3 class="mt-5 font-bold text-base tracking-tight text-slate-800 dark:text-slate-100">
        Turikale Print Layouting
    </h3>

    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        Memuat kanvas presisi...
    </p>

</div>

    <header class="no-print sticky top-0 left-0 w-full z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 shadow-sm">
        <div class="max-w-[1750px] mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">

            <div class="flex items-center gap-3 shrink-0">
                <a href="../index.php"
        class="flex items-center justify-center gap-2 text-slate-700 dark:text-slate-200 hover:text-primary transition-colors"
        aria-label="Kembali ke Beranda Turikale Print">
        <img
            src="../assets/images/Logo hitam-TurikalePrint.png"
            alt="Turikale Print"
            class="h-10 w-auto" />
    </a>
            </div>

            <nav class="hidden md:flex items-center gap-1.5 lg:gap-3 bg-slate-100/90 dark:bg-slate-800/80 p-1.5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                <button id="nav-step-1" onclick="App.goToStep(1)" class="nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm">
                    <span class="size-5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[11px] font-bold flex items-center justify-center">1</span>
                    <span>1. Unggah Foto</span>
                </button>
                <div class="w-4 h-0.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                <button id="nav-step-2" onclick="App.goToStep(2)" class="nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    <span class="size-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold flex items-center justify-center">2</span>
                    <span>2. Edit & Ukuran</span>
                </button>
                <div class="w-4 h-0.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                <button id="nav-step-3" onclick="App.goToStep(3)" class="nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    <span class="size-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold flex items-center justify-center">3</span>
                    <span>3. Tata Letak Cetak</span>
                </button>
            </nav>

            <div class="flex items-center gap-2 shrink-0">

                <div class="md:hidden flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 border border-slate-200/60">
                    <span class="size-2 rounded-full bg-slate-900 dark:bg-white animate-pulse"></span>
                    <span id="mobile-step-title">Langkah 1/3</span>
                </div>

                <button onclick="App.openFeedbackModal()"
            class="p-2 sm:px-3 sm:py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1.5 text-xs font-medium"
            title="Kirim Saran atau Lapor Masalah">
                    <span class="material-symbols-outlined text-[17px] group-hover:rotate-12 transition-transform">feedback</span>
                    <span class="text-xs font-bold tracking-tight hidden sm:inline">Kirim Saran</span>

        </button>

                <button onclick="App.toggleShortcutModal(true)" class="p-2 sm:px-3 sm:py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1.5 text-xs font-medium" title="Bantuan & Pintasan Keyboard">
                    <span class="material-symbols-outlined text-[18px]">keyboard</span>
                    <span class="hidden sm:inline">Pintasan</span>
                </button>

                <button onclick="App.resetProject()" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-rose-950/40 dark:text-slate-300 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 text-xs font-semibold" title="Mulai Ulang Kanvas">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    <span class="hidden sm:inline">Mulai Baru</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 flex flex-col relative w-full h-full">

        <main id="step-upload" class="step-view flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 flex flex-col items-center justify-center">

            <div class="text-center mb-8 sm:mb-12 max-w-2xl mx-auto">

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
                    Mulai dengan Mengunggah Foto
                </h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                    Tarik dan lepas foto ke area di bawah ini. Dapat mengunggah banyak foto secara bertahap tanpa menghapus foto yang sudah ada di kanvas cetak.
                </p>
            </div>

            <div id="drop-zone" class="w-full max-w-3xl bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-slate-900 dark:hover:border-white rounded-3xl p-8 sm:p-14 flex flex-col items-center justify-center text-center cursor-pointer group relative overflow-hidden shadow-floating transition-all duration-200">
                <input type="file" id="photo-upload" class="hidden" accept="image/*, .heic, .heif, .jpg, .jpeg, .png, .webp" onchange="App.handleImageUpload(event)">

                <div class="size-20 sm:size-24 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center mb-6 group-hover:scale-105 group-hover:bg-slate-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-slate-900 transition-all duration-200 shadow-sm">
                    <span class="material-symbols-outlined text-4xl sm:text-5xl">cloud_upload</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    Tarik & Lepas Foto di Sini
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-md">
                    atau klik Pilih Berkas untuk memilih file foto dari perangkat (JPG, PNG, WEBP, atau iPhone HEIC hingga 50MB)
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <button type="button" onclick="document.getElementById('photo-upload').click()" class="bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white font-semibold text-xs sm:text-sm py-3.5 px-8 rounded-2xl shadow-sm hover:scale-[1.01] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">add_photo_alternate</span>
                        Pilih Berkas
                    </button>

                </div>
            </div>

        </main>

        <main id="step-size" class="step-view hidden flex-1 w-full max-w-[1750px] mx-auto p-3 sm:p-6 lg:p-8 flex flex-col lg:flex-row gap-5 items-center lg:items-stretch justify-center h-full overflow-y-auto lg:overflow-hidden">

            <div class="lg:hidden w-full flex items-center justify-around bg-slate-200/70 dark:bg-slate-800/80 p-1 rounded-xl mb-1 text-xs font-semibold shrink-0">
                <button onclick="App.switchMobileEditTab('tools')" id="tab-btn-tools" class="flex-1 py-2 px-3 rounded-lg text-slate-600 dark:text-slate-400 text-center transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">tune</span> Filter & Warna
                </button>
                <button onclick="App.switchMobileEditTab('cropper')" id="tab-btn-cropper" class="flex-1 py-2 px-3 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm text-center transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">crop</span> Potong / Crop
                </button>
                <button onclick="App.switchMobileEditTab('sizes')" id="tab-btn-sizes" class="flex-1 py-2 px-3 rounded-lg text-slate-600 dark:text-slate-400 text-center transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">format_list_numbered</span> Ukuran & Jumlah
                </button>
            </div>

            <aside id="panel-tools" class="w-full lg:w-[380px] xl:w-[420px] flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm overflow-hidden shrink-0 h-auto lg:h-full max-h-none lg:max-h-[calc(100vh-140px)]">
                <header class="mb-3 shrink-0 flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-slate-900 dark:text-white text-lg">auto_fix_high</span>
                            Penyempurnaan Foto
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Poles foto dengan filter & pencahayaan presisi</p>
                    </div>
                    <button onclick="App.resetEnhancements()" class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-1" title="Reset semua filter">
                        <span class="material-symbols-outlined text-[16px]">refresh</span> Reset
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto hide-scrollbar space-y-5 pr-1">
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/80 dark:to-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-brand-600 dark:text-brand-400">magic_button</span>
                                Hapus Latar AI
                            </label>
                            <span id="ai-model-status-badge" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">ðŸ”´ AI Belum Diunduh</span>
                        </div>

                        <div id="ai-inline-progress" class="hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80 rounded-xl p-3 space-y-2 shadow-sm transition-all">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5 truncate">
                                    <span id="ai-inline-spinner" class="material-symbols-outlined text-[16px] text-blue-600 dark:text-blue-400 animate-spin">autorenew</span>
                                    <span id="ai-inline-status-text" class="truncate">Menyiapkan model AI...</span>
                                </span>
                                <span id="ai-inline-percentage" class="text-xs font-extrabold text-blue-600 dark:text-blue-400 shrink-0">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-200 dark:border-slate-700">
                                <div id="ai-inline-bar-fill" class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full transition-all duration-150 ease-out" style="width: 0%;"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400">
                                <span id="ai-inline-detail" class="truncate">Mengunduh modul WebAssembly...</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button id="btn-remove-bg" onclick="App.removeBackgroundAI()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-xs py-2.5 px-3.5 rounded-xl shadow-sm hover:shadow active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                                <span id="btn-remove-bg-text">Hapus Latar Belakang</span>
                            </button>
                            <div class="grid grid-cols-2 gap-2">
                                <button id="btn-manual-edit" onclick="App.openManualEditModal()" class="w-full bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs py-2 px-2.5 rounded-xl transition-all flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-amber-500">brush</span>
                                    <span>Edit Manual</span>
                                </button>
                                <button id="btn-restore-original" onclick="App.restoreOriginalPhoto()" class="w-full bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs py-2 px-2.5 rounded-xl transition-all flex items-center justify-center gap-1" title="Kembalikan foto ke versi asli">
                                    <span class="material-symbols-outlined text-[15px] text-slate-500">undo</span>
                                    <span>Foto Asli</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-2.5">
                            Filter Warna Instan
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="App.applyFilter('auto')" class="col-span-2 bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white rounded-xl p-3 flex items-center justify-center gap-2 font-semibold text-xs shadow-sm active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[18px]">magic_button</span>
                                <span>Auto Polish (Optimal)</span>
                            </button>
                            <button onclick="App.applyFilter('brighten')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-amber-500 text-xl">light_mode</span>
                                <span>Cerahkan</span>
                            </button>
                            <button onclick="App.applyFilter('contrast')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-indigo-500 text-xl">contrast</span>
                                <span>Kontras +</span>
                            </button>
                            <button onclick="App.applyFilter('warm')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-orange-500 text-xl">wb_sunny</span>
                                <span>Nada Hangat</span>
                            </button>
                            <button onclick="App.applyFilter('cool')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-cyan-500 text-xl">ac_unit</span>
                                <span>Nada Dingin</span>
                            </button>
                            <button onclick="App.applyFilter('vivid')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-emerald-500 text-xl">palette</span>
                                <span>Tajam & Vivid</span>
                            </button>
                            <button onclick="App.applyFilter('bw')" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-slate-600 dark:text-slate-300 text-xl">filter_b_and_w</span>
                                <span>Hitam Putih (B&W)</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                            Penyesuaian Manual
                        </label>
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                <span>Kecerahan (Brightness)</span>
                                <span id="val-brightness">100%</span>
                            </div>
                            <input type="range" id="slider-brightness" min="50" max="160" value="100" oninput="App.handleManualAdjustment()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                <span>Kontras (Contrast)</span>
                                <span id="val-contrast">100%</span>
                            </div>
                            <input type="range" id="slider-contrast" min="50" max="160" value="100" oninput="App.handleManualAdjustment()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                <span>Saturasi Warna (Saturation)</span>
                                <span id="val-saturate">100%</span>
                            </div>
                            <input type="range" id="slider-saturate" min="0" max="200" value="100" oninput="App.handleManualAdjustment()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                <span>Kehangatan Warna (Warmth)</span>
                                <span id="val-sepia">0%</span>
                            </div>
                            <input type="range" id="slider-sepia" min="0" max="80" value="0" oninput="App.handleManualAdjustment()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                        </div>
                    </div>

                </div>
            </aside>

            <section id="panel-cropper" class="w-full lg:flex-1 max-w-[560px] h-[480px] sm:h-[520px] bg-slate-900 rounded-3xl relative flex items-center justify-center overflow-hidden border border-slate-800 shadow-sm shrink-0 self-center touch-none select-none overscroll-contain">

                <div class="absolute inset-0 pointer-events-none border border-white/5 m-8 hidden md:block z-10">
                    <div class="absolute top-1/3 left-0 w-full border-t border-white/10 border-dashed"></div>
                    <div class="absolute top-2/3 left-0 w-full border-t border-white/10 border-dashed"></div>
                    <div class="absolute left-1/3 top-0 h-full border-l border-white/10 border-dashed"></div>
                    <div class="absolute left-2/3 top-0 h-full border-l border-white/10 border-dashed"></div>
                </div>

                <div class="w-full h-full flex items-center justify-center relative p-3 sm:p-4 touch-none select-none overscroll-contain">
                    <img id="cropper-image" alt="Preview Foto Potong" class="w-full h-full object-contain hidden max-h-[440px] touch-none" src="" />
                </div>

                <div class="absolute top-4 left-4 z-20 flex items-center gap-2">
                    <div class="bg-black/75 backdrop-blur-md text-white px-3.5 py-1.5 rounded-full border border-white/20 flex items-center gap-2 text-xs font-semibold shadow-sm">
                        <span class="material-symbols-outlined text-[16px] text-white">crop</span>
                        <span id="cropper-ratio-label">Rasio: 3x4 cm</span>
                    </div>
                </div>

                <div class="absolute bottom-4 sm:bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20 bg-slate-900/90 backdrop-blur-xl p-1.5 rounded-2xl border border-white/20 shadow-2xl">
                    <button onclick="App.rotateCropper(-90)" class="p-2 rounded-xl hover:bg-white/10 text-white transition-colors" title="Putar Kiri 90Â°">
                        <span class="material-symbols-outlined text-[18px]">rotate_left</span>
                    </button>
                    <button onclick="App.rotateCropper(90)" class="p-2 rounded-xl hover:bg-white/10 text-white transition-colors" title="Putar Kanan 90Â°">
                        <span class="material-symbols-outlined text-[18px]">rotate_right</span>
                    </button>
                    <button onclick="App.flipCropper('h')" class="p-2 rounded-xl hover:bg-white/10 text-white transition-colors" title="Cermin Horisontal">
                        <span class="material-symbols-outlined text-[18px]">flip</span>
                    </button>

                    <div class="w-px h-5 bg-white/20 mx-0.5"></div>

                    <button id="btn-confirm-crop" onclick="App.confirmCurrentFrame()" class="bg-white text-slate-900 hover:bg-slate-100 px-3.5 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-lg active:scale-95 transition-all animate-pulse-confirm" title="Konfirmasi & Kunci Posisi Frame untuk Ukuran Ini">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                        <span>Konfirmasi Frame</span>
                    </button>
                </div>
            </section>

            <aside id="panel-sizes" class="w-full lg:w-[380px] xl:w-[420px] flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm overflow-hidden shrink-0 h-auto lg:h-full max-h-none lg:max-h-[calc(100vh-140px)]">
                <header class="mb-3 shrink-0 pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-slate-900 dark:text-white text-lg">grid_view</span>
                            Ukuran & Jumlah Cetak
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pilih ukuran & konfirmasi frame foto</p>
                    </div>

                </header>

                <div class="flex-1 overflow-y-auto hide-scrollbar space-y-4 pr-1">

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-2">
                            Warna Latar Pas Foto
                        </label>
                        <div class="flex items-center gap-2.5">

                            <button onclick="App.selectBgColor('transparent', this)" class="bg-color-btn size-9 rounded-full border-2 border-slate-900 dark:border-white flex items-center justify-center relative shadow-sm" style="background: repeating-conic-gradient(#cbd5e1 0% 25%, white 0% 50%) 50% / 10px 10px;" title="Latar Transparan / Asli">
                                <span class="material-symbols-outlined text-slate-900 text-[18px]">check</span>
                            </button>

                            <button onclick="App.selectBgColor('#DB1514', this)" class="bg-color-btn size-9 rounded-full border-2 border-transparent bg-red-600 flex items-center justify-center relative shadow-sm" title="Merah Formal Pas Foto ">
                                <span class="material-symbols-outlined text-white text-[18px] hidden">check</span>
                            </button>

                            <button onclick="App.selectBgColor('#0000FF', this)" class="bg-color-btn size-9 rounded-full border-2 border-transparent bg-blue-600 flex items-center justify-center relative shadow-sm" title="Biru Formal Pas Foto">
                                <span class="material-symbols-outlined text-white text-[18px] hidden">check</span>
                            </button>

                            <button onclick="App.selectBgColor('#FFFFFF', this)" class="bg-color-btn size-9 rounded-full border-2 border-transparent bg-white border-slate-300 flex items-center justify-center relative shadow-sm" title="Putih Bersih">
                                <span class="material-symbols-outlined text-slate-800 text-[18px] hidden">check</span>
                            </button>

                            <div class="relative">
                                <input type="color" id="custom-color-picker" onchange="App.handleCustomColor(event)" class="opacity-0 absolute inset-0 size-9 cursor-pointer">
                                <div class="size-9 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-gradient-to-tr from-pink-500 via-amber-400 to-cyan-400 flex items-center justify-center shadow-sm cursor-pointer" title="Pilih Warna Kustom">
                                    <span class="material-symbols-outlined text-white text-[16px]">palette</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Pilih Ukuran & Jumlah Foto
                            </label>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Klik kotak lalu konfirmasi frame</span>
                        </div>

                        <div id="size-cards-container" class="grid grid-cols-3 gap-2">

                        </div>
                    </div>

                </div>

                <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 shrink-0 flex items-center justify-between gap-3">
                    <div class="flex flex-col">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Total Foto Dipilih:</span>
                        <span id="total-qty-label" class="text-lg font-bold text-slate-900 dark:text-white leading-tight">0 Foto</span>
                    </div>
                    <button onclick="App.confirmAndGoToPrint()" class="bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white font-semibold text-xs sm:text-sm py-3 px-5 rounded-2xl shadow-sm hover:scale-[1.01] active:scale-95 transition-all flex items-center gap-2">
                        <span>Lanjut ke Cetak</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </aside>
        </main>

        <main id="step-print" class="step-view hidden flex-1 w-full max-w-[1750px] mx-auto flex flex-col lg:flex-row h-full overflow-hidden">

            <div class="flex-1 canvas-grid-pattern flex flex-col relative overflow-hidden h-full min-h-[400px]">

                <div class="no-print absolute top-3 mb-12 left-4 right-4 z-30 flex flex-wrap items-center justify-between gap-2 pointer-events-none">

                    <div class="pointer-events-auto bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 py-2 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-slate-700 dark:text-slate-300 text-lg">description</span>
                            <span id="current-paper-badge" class="text-xs font-bold text-slate-900 dark:text-white">A4 (210 x 297 mm)</span>
                        </div>
                        <span class="text-slate-300 dark:text-slate-700">|</span>
                        <span id="canvas-photo-count-badge" class="text-xs font-semibold text-slate-500 dark:text-slate-400">0 Foto di Lembar</span>
                    </div>

                    <div class="pointer-events-auto flex items-center gap-2">

                        <button onclick="App.triggerAddAnotherPhoto()" class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-3 py-2 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" title="Upload foto lain untuk ditambahkan ke lembar ini">
                            <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
                            <span>+ Unggah Foto Lain</span>
                        </button>

                        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-1 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center">
                            <button onclick="App.undo()" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors" title="Urungkan (Ctrl+Z)">
                                <span class="material-symbols-outlined text-[18px]">undo</span>
                            </button>
                            <button onclick="App.redo()" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors" title="Ulangi (Ctrl+Y)">
                                <span class="material-symbols-outlined text-[18px]">redo</span>
                            </button>
                        </div>

                        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-1 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-1">
                            <button onclick="App.zoomCanvas(-0.1)" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors" title="Perkecil (-)">
                                <span class="material-symbols-outlined text-[18px]">remove</span>
                            </button>
                            <span id="zoom-level-text" class="text-xs font-bold text-slate-800 dark:text-slate-200 px-2 min-w-[45px] text-center">100%</span>
                            <button onclick="App.zoomCanvas(0.1)" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors" title="Perbesar (+)">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                            </button>
                            <button onclick="App.fitCanvasToScreen()" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors border-l border-slate-200 dark:border-slate-700 pl-2" title="Pas ke Layar">
                                <span class="material-symbols-outlined text-[18px]">fit_screen</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="paper-viewport" class="flex-1 w-full h-full overflow-auto flex items-center justify-center p-9 sm:p-12 lg:p-16 relative" onmousedown="App.handleViewportMouseDown(event)">

                    <div id="paper-workspace" class="relative transition-transform duration-100 origin-center my-auto touch-none select-none">

                        <div id="paper-canvas" class="bg-white shadow-paper relative overflow-hidden transition-all duration-200 touch-none select-none" style="width: 210mm; height: 297mm;">

                            <div id="snap-guide-v" class="snap-guide snap-guide-v hidden"></div>
                            <div id="snap-guide-h" class="snap-guide snap-guide-h hidden"></div>

                            <div id="cut-marks-container" class="absolute inset-0 pointer-events-none z-30"></div>

                            <div id="canvas-photos-layer" class="absolute inset-0 z-20 touch-none select-none"></div>

                            <div id="marquee-box" class="marquee-selection-box hidden"></div>

                            <div id="floating-photo-toolbar" class="no-print hidden absolute z-50 bg-slate-900/95 text-white backdrop-blur-md px-1.5 py-1 rounded-xl border border-white/20 shadow-xl flex items-center gap-1 -translate-x-1/2 -translate-y-full pointer-events-auto">
                                <button onclick="App.rotateSelectedPhoto(-90)" class="p-1 hover:bg-white/20 rounded-lg text-white flex items-center justify-center active:scale-90 transition-all" title="Putar Kiri 90Â°">
                                    <span class="material-symbols-outlined text-[18px]">rotate_left</span>
                                </button>
                                <div class="w-px h-3.5 bg-white/20"></div>
                                <button onclick="App.rotateSelectedPhoto(90)" class="p-1 hover:bg-white/20 rounded-lg text-white flex items-center justify-center active:scale-90 transition-all" title="Putar Kanan 90Â°">
                                    <span class="material-symbols-outlined text-[18px]">rotate_right</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <aside id="print-sidebar" class="no-print w-full lg:w-[410px] xl:w-[460px] bg-white dark:bg-slate-900 border-t lg:border-t-0 lg:border-l border-slate-200/80 dark:border-slate-800 flex flex-col h-full overflow-hidden shadow-sm z-40">

                <header class="p-5 border-b border-slate-100 dark:border-slate-800 shrink-0 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-700 dark:text-slate-300 text-xl">tune</span>
                            Pengaturan Lembar Cetak
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur format kertas & tata letak foto presisi</p>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto hide-scrollbar p-5 space-y-5">

                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/70 dark:border-slate-700/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Format Kertas</span>
                            <div class="flex items-center gap-1 bg-white dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                                <button onclick="App.setPaperOrientation('portrait')" id="btn-orient-p" class="p-1.5 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition-all text-xs font-semibold flex items-center gap-1" title="Orientasi Potret">
                                    <span class="material-symbols-outlined text-[16px]">crop_portrait</span>
                                </button>
                                <button onclick="App.setPaperOrientation('landscape')" id="btn-orient-l" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs font-semibold flex items-center gap-1" title="Orientasi Lanskap">
                                    <span class="material-symbols-outlined text-[16px]">crop_landscape</span>
                                </button>
                            </div>
                        </div>

                        <select id="paper-size-select" onchange="App.handlePaperSizeChange()" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white">
                            <option value="A4" data-w="210" data-h="297" selected>Kertas A4 (210 x 297 mm) - Standar</option>
                            <option value="F4" data-w="215" data-h="330">Kertas F4 / Folio (215 x 330 mm)</option>
                            <option value="A3" data-w="297" data-h="420">Kertas A3 (297 x 420 mm) - Besar</option>
                            <option value="A3+" data-w="329" data-h="483">Kertas A3+ Super (329 x 483 mm)</option>
                            <option value="Letter" data-w="215.9" data-h="279.4">Kertas Letter (215.9 x 279.4 mm)</option>
                            <option value="4R" data-w="102" data-h="152">Kertas Foto 4R (102 x 152 mm)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <button onclick="App.autoArrangePhotos(true)" class="w-full bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white rounded-2xl py-3.5 px-4 font-bold text-xs sm:text-sm shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">

                            <span>Otomatis Rapikan Tata Letak</span>
                        </button>

                        <div class="grid grid-cols-3 gap-1.5">
                            <button onclick="App.selectAllPhotos()" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">select_all</span>
                                <span>Pilih Semua</span>
                            </button>
                            <button onclick="App.duplicateSelectedPhotos()" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                <span>Duplikat</span>
                            </button>

                            <button onclick="App.deleteSelectedPhotos()" class="p-2 rounded-xl border border-rose-200 dark:border-rose-900/60 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">delete</span>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm space-y-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-slate-700 dark:text-slate-300 text-lg">content_cut</span>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Garis Potong</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Garis putus-putus presisi untuk memotong hasil cetak</p>
                                </div>
                            </div>
                            <input type="checkbox" id="cut-guide-toggle" checked onchange="App.updateCutGuideSettings()" class="size-4 rounded text-slate-900 focus:ring-slate-900 accent-slate-900 dark:accent-white">
                        </div>

                        <div id="cut-guide-controls" class="space-y-3.5 pt-2 border-t border-slate-100 dark:border-slate-800">

                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">opacity</span>
                                        Transparansi Garis Potong (Opacity)
                                    </span>
                                    <span id="label-cut-opacity" class="font-bold text-slate-900 dark:text-white">25%</span>
                                </div>
                                <input type="range" id="slider-cut-opacity" min="1" max="30" step="1" value="25" oninput="App.updateCutGuideSettings('opacity')" onchange="App.recordHistory()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                            </div>

                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">open_in_full</span>
                                        Jarak Garis ke Tepi Foto (Gap Luar)
                                    </span>
                                    <span id="label-cut-offset" class="font-bold text-slate-900 dark:text-white">1.5 mm</span>
                                </div>
                                <input type="range" id="slider-cut-offset" min="0" max="5.0" step="0.2" value="1.5" oninput="App.updateCutGuideSettings('offset')" onchange="App.recordHistory()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                            </div>

                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">space_bar</span>
                                        Jarak Antar Foto (Gap)
                                    </span>
                                    <span id="label-photo-gap" class="font-bold text-slate-900 dark:text-white">3 mm</span>
                                </div>
                                <input type="range" id="slider-photo-gap" min="0" max="15" step="0.1" value="3" oninput="App.updateCutGuideSettings('gap')" onchange="App.recordHistory()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                            </div>

                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">border_outer</span>
                                        Margin Tepi Kertas
                                    </span>
                                    <span id="label-paper-margin" class="font-bold text-slate-900 dark:text-white">5 mm</span>
                                </div>
                                <input type="range" id="slider-paper-margin" min="0" max="30" step="1" value="5" oninput="App.updateCutGuideSettings('margin')" onchange="App.recordHistory()" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-slate-900 dark:accent-white">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md shrink-0 space-y-2.5">
                    <button id="feedback" onclick="App.executePrint()" class="w-full bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white rounded-2xl py-3.5 px-4 font-bold text-xs sm:text-sm shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>Cetak Sekarang</span>
                    </button>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="App.exportPaperImage()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" title="Simpan lembar cetak utuh dalam resolusi tinggi 300 DPI">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh Lembar HD</span>
                        </button>
                        <button onclick="App.exportSingleCroppedImage()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" title="Simpan foto terakhir yang diedit">
                            <span class="material-symbols-outlined text-[16px]">image</span>
                            <span>Unduh Foto Satuan</span>
                        </button>
                    </div>
                </div>
            </aside>
        </main>

    </div>

    <div id="modal-size-warning" class="fixed inset-0 z-[9995] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-start gap-3.5">
                <div class="size-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Ukuran Foto Melebihi Kertas</h3>
                    <p id="size-warning-message" class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                        Ukuran foto yang dipilih lebih besar dari format kertas saat ini.
                    </p>
                </div>
            </div>

            <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-2xl p-3.5 text-xs text-amber-900 dark:text-amber-200 space-y-1.5">
                <p class="font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">lightbulb</span>
                    Saran & Solusi:
                </p>
                <p>Gunakan format kertas yang lebih besar (misalnya <strong>Kertas A3</strong> atau <strong>A3+ Super</strong>) agar foto dapat dicetak penuh.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button onclick="App.toggleSizeWarningModal(false)" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Tetap Lanjutkan
                </button>
                <button onclick="App.autoSwitchToLargerPaper()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all">
                    Ubah ke Kertas A3
                </button>
            </div>
        </div>
    </div>

    <div id="modal-shortcut" class="fixed inset-0 z-[9990] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-700 dark:text-slate-300">keyboard</span>
                    Pintasan Keyboard
                </h3>
                <button onclick="App.toggleShortcutModal(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-600 dark:text-slate-400">Pilih Semua Foto di Kanvas</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Ctrl + A</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-600 dark:text-slate-400">Duplikasi Foto Terpilih</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Ctrl + D</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-600 dark:text-slate-400">Urungkan Aksi (Undo)</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Ctrl + Z</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-600 dark:text-slate-400">Ulangi Aksi (Redo)</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Ctrl + Y</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-600 dark:text-slate-400">Hapus Foto Terpilih</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Delete / Backspace</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-slate-600 dark:text-slate-400">Batalkan Seleksi</span>
                    <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded font-mono font-bold">Esc</kbd>
                </div>
            </div>

            <button onclick="App.toggleShortcutModal(false)" class="w-full py-2.5 bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white rounded-xl font-semibold text-xs transition-colors">
                Saya Mengerti
            </button>
        </div>
    </div>

    <div id="modal-manual-edit" class="fixed inset-0 z-[9997] bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-5xl h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-900/90">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">brush</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-white">Edit Manual Latar Belakang</h3>
                        <p class="text-[11px] text-slate-400">Gunakan penghapus atau kuas pemulih untuk merapikan tepian objek</p>
                    </div>
                </div>
                <button onclick="App.closeManualEditModal()" class="text-slate-400 hover:text-white p-1.5 rounded-xl hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-5 py-3 border-b border-slate-800 bg-slate-950/60 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2">
                    <button id="tool-btn-eraser" onclick="App.setManualEditTool('eraser')" class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-blue-600 text-white shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">ink_eraser</span>
                        <span>Penghapus (Eraser)</span>
                    </button>
                    <button id="tool-btn-restore" onclick="App.setManualEditTool('restore')" class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[16px]">history_edu</span>
                        <span>Kuas Pemulih (Restore)</span>
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 min-w-[170px]">
                        <span class="text-xs font-medium text-slate-400">Ukuran:</span>
                        <input type="range" id="manual-brush-size" min="3" max="150" value="20" oninput="App.handleManualBrushSizeChange(this.value)" class="w-24 sm:w-32 h-1.5 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500">
                        <span id="manual-brush-size-val" class="text-xs font-bold text-slate-200 min-w-[36px]">20px</span>
                    </div>
                    <div class="flex items-center gap-1 bg-slate-800/80 p-1 rounded-xl border border-slate-700/80">
                        <button onclick="App.zoomManualEdit(-0.25)" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Perkecil (-)">
                            <span class="material-symbols-outlined text-[16px]">remove</span>
                        </button>
                        <span id="manual-zoom-val" class="text-xs font-bold text-slate-200 min-w-[42px] text-center">100%</span>
                        <button onclick="App.zoomManualEdit(0.25)" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Perbesar (+)">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                        </button>
                        <button onclick="App.fitManualEditZoom()" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border-l border-slate-700 pl-2" title="Pas ke Layar">
                            <span class="material-symbols-outlined text-[16px]">fit_screen</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="App.undoManualEdit()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Urungkan (Undo)">
                            <span class="material-symbols-outlined text-[18px]">undo</span>
                        </button>
                        <button onclick="App.redoManualEdit()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Ulangi (Redo)">
                            <span class="material-symbols-outlined text-[18px]">redo</span>
                        </button>
                    </div>
                </div>
            </div>
            <div id="manual-canvas-container" class="flex-1 relative overflow-auto flex items-center justify-center p-4 select-none" style="background: repeating-conic-gradient(#1e293b 0% 25%, #0f172a 0% 50%) 50% / 20px 20px;">
                <div id="manual-canvas-wrapper" class="relative inline-block m-auto">
                    <canvas id="manual-edit-canvas" class="rounded-xl shadow-2xl border border-white/10 block" style="cursor: none;"></canvas>
                </div>
            </div>
            <div id="manual-brush-cursor" class="pointer-events-none fixed z-[99999] rounded-full -translate-x-1/2 -translate-y-1/2 hidden"></div>
            <div class="px-5 py-3.5 border-t border-slate-800 bg-slate-900/95 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-400 hidden sm:inline">Tekan & geser mouse pada kanvas. Gunakan scroll mouse untuk zoom presisi.</span>
                <div class="flex items-center gap-2 ml-auto">
                    <button onclick="App.closeManualEditModal()" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button onclick="App.saveManualEdit()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md active:scale-95 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        <span>Simpan Hasil Edit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-custom-dialog" class="fixed inset-0 z-[9998] bg-black/70 backdrop-blur-md flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 transform transition-all duration-200 scale-95" id="custom-dialog-box">
            <div class="flex items-start gap-3.5">
                <div id="dialog-icon-container" class="size-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <span id="dialog-icon" class="material-symbols-outlined text-2xl">help</span>
                </div>
                <div class="flex-1">
                    <h3 id="dialog-title" class="font-bold text-base text-slate-900 dark:text-white"></h3>
                    <p id="dialog-message" class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed"></p>
                </div>
            </div>
            <div id="dialog-prompt-container" class="hidden">
                <input type="text" id="dialog-prompt-input" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900 dark:focus:ring-white">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button id="dialog-btn-cancel" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Batal</button>
                <button id="dialog-btn-confirm" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all">Konfirmasi</button>
            </div>
        </div>
    </div>

    <div id="modal-feedback" class="no-print fixed inset-0 z-[9998] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200" onclick="if(event.target === this) App.closeFeedbackModal()">
        <div id="feedback-modal-box" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 max-w-md w-full shadow-2xl space-y-4 transform transition-all duration-200 scale-95">

            <header class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl">feedback</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Kirim Saran & Lapor Masalah</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukan Anda langsung membantu peningkatan sistem</p>
                    </div>
                </div>
                <button type="button" onclick="App.closeFeedbackModal()" class="size-8 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-colors" title="Tutup">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </header>

            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label for="feedback-nama" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Nama (Opsional)</label>
                        <input type="text" id="feedback-nama" placeholder="Nama Anda" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-slate-900 dark:focus:ring-white outline-none transition-all" />
                    </div>
                    <div>
                        <label for="feedback-kontak" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">No. WhatsApp / Kontak (Opsional)</label>
                        <input type="text" id="feedback-kontak" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-slate-900 dark:focus:ring-white outline-none transition-all" />
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1.5">Kategori Masukan</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5" id="feedback-category-group">
                        <button type="button" onclick="App.selectFeedbackCategory('Saran Fitur', this)" class="feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center bg-slate-900 text-white dark:bg-white dark:text-slate-900 border-slate-900 dark:border-white shadow-xs">Saran Fitur</button>
                        <button type="button" onclick="App.selectFeedbackCategory('Lapor Bug', this)" class="feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Lapor Bug</button>
                        <button type="button" onclick="App.selectFeedbackCategory('Garis Potong', this)" class="feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Garis Potong</button>
                        <button type="button" onclick="App.selectFeedbackCategory('Lainnya', this)" class="feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Lainnya</button>
                    </div>
                </div>

                <div>
                    <label for="feedback-message" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Catatan / Detail Masukan</label>
                    <textarea id="feedback-message" rows="4" maxlength="500"
                        placeholder="Tuliskan saran di sini..."
                        class="w-full text-xs p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-slate-900 dark:focus:ring-white outline-none resize-none transition-all"></textarea>
                    <div class="flex justify-between items-center mt-1 text-[10px] text-slate-400">

                        <span id="feedback-char-count">0 / 500</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="App.closeFeedbackModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Batal</button>
                <button type="button" id="btn-submit-feedback" onclick="App.submitFeedback()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-bold shadow-md active:scale-95 transition-all flex items-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Kirim Saran</span>
                </button>
            </div>

        </div>
    </div>

 <div id="modal-genz-eval" class="no-print fixed inset-0 z-[99999] bg-slate-950/70 backdrop-blur-xl flex items-center justify-center p-3 sm:p-5 opacity-0 pointer-events-none transition-all duration-500" onclick="if(event.target === this) closeGenZEvalModal()" <?= (isset($evalConfig['enabled']) && !$evalConfig['enabled']) ? 'style="display:none !important;"' : '' ?>>

    <div id="genz-modal-box" class="relative w-full max-w-2xl bg-slate-900/60 dark:bg-slate-900/70 text-white rounded-[2rem] p-5 sm:p-8 shadow-2xl max-h-[92vh] flex flex-col scale-95 transition-transform duration-500 overflow-hidden font-sans border border-white/10 backdrop-blur-2xl">

        <div class="pointer-events-none absolute inset-0 -z-10 opacity-60">
            <div class="absolute -top-24 -left-16 w-72 h-72 bg-fuchsia-500 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute top-1/3 -right-20 w-72 h-72 bg-cyan-400 rounded-full blur-[110px] animate-pulse [animation-delay:1s]"></div>
            <div class="absolute -bottom-24 left-1/3 w-72 h-72 bg-violet-600 rounded-full blur-[110px] animate-pulse [animation-delay:2s]"></div>
        </div>

        <button type="button" onclick="closeGenZEvalModal()" class="absolute top-4 right-4 size-10 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-base backdrop-blur-md hover:scale-105 active:scale-90 transition-all flex items-center justify-center z-10 cursor-pointer" title="Tutup">
            ✕
        </button>

        <div class="pr-12 pb-4 border-b border-white/10 shrink-0">
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="px-3 py-1 bg-gradient-to-r from-fuchsia-500 to-violet-600 text-white font-bold text-[11px] rounded-full shadow-lg shadow-fuchsia-500/30 uppercase tracking-wider">
                    ⚡ Quick Review Admin
                </span>
                <span class="px-3 py-1 bg-white/10 text-cyan-200 font-semibold text-[11px] rounded-full border border-white/10 backdrop-blur-sm" id="genz-modal-title-badge">
                    <?= htmlspecialchars($evalConfig['title'] ?? 'Web vs Photoshop') ?>
                </span>
            </div>
            <h3 class="font-extrabold text-lg sm:text-xl bg-gradient-to-r from-white via-cyan-100 to-fuchsia-200 bg-clip-text text-transparent leading-tight" id="genz-modal-subtitle">
                <?= htmlspecialchars($evalConfig['subtitle'] ?? '🖨️ Cetakan Meluncur! Review jujur web ini vs Photoshop dong!') ?>
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1.5" id="genz-modal-description">
                <?= htmlspecialchars($evalConfig['description'] ?? 'Bantu kami bikin tool cetak paling sat-set! Klik 1 opsi per pertanyaan di bawah (cuma butuh 5 detik!):') ?>
            </p>
        </div>

        <div id="genz-questions-container" class="flex-1 overflow-y-auto py-4 space-y-4 pr-1 [scrollbar-width:thin] [scrollbar-color:theme(colors.fuchsia.500)_transparent]">

        </div>

        <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
                <span id="genz-eval-progress" class="text-xs font-bold text-slate-200 bg-white/10 px-3 py-1.5 rounded-full border border-white/10 backdrop-blur-sm">
                    0 / 5 terjawab
                </span>
                <button type="button" onclick="closeGenZEvalModal()" class="sm:hidden px-3.5 py-1.5 rounded-full bg-white/10 text-slate-200 border border-white/10 font-semibold text-xs backdrop-blur-sm cursor-pointer">
                    Lewati dulu 🏃💨
                </button>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <button type="button" onclick="closeGenZEvalModal()" class="hidden sm:inline-flex px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-slate-200 border border-white/10 font-semibold text-xs backdrop-blur-sm hover:scale-[1.03] active:scale-95 transition-all cursor-pointer">
                    Lewati dulu 🏃💨
                </button>
                <button type="button" id="btn-submit-genz-eval" onclick="submitGenZEval()" disabled class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-gradient-to-r from-fuchsia-500 via-violet-500 to-cyan-400 text-white border border-white/20 font-bold text-xs sm:text-sm shadow-lg shadow-fuchsia-500/40 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none hover:brightness-110 hover:scale-[1.03] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span>Kirim Review 🚀</span>
                </button>
            </div>
        </div>

        <div id="genz-success-overlay" class="absolute inset-0 bg-slate-950/90 backdrop-blur-2xl rounded-[2rem] p-6 flex flex-col items-center justify-center text-center opacity-0 pointer-events-none transition-all duration-500 z-20">
            <div class="pointer-events-none absolute inset-0 -z-10 opacity-60 rounded-[2rem] overflow-hidden">
                <div class="absolute top-0 left-0 w-64 h-64 bg-lime-400 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-0 w-64 h-64 bg-cyan-400 rounded-full blur-[100px]"></div>
            </div>
            <div class="size-20 bg-gradient-to-br from-lime-400 to-cyan-400 rounded-3xl shadow-xl shadow-lime-400/40 flex items-center justify-center text-4xl mb-4 animate-bounce">
                💸
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-lime-300 to-cyan-300 bg-clip-text text-transparent tracking-tight">
                ✨ Makasih! Lanjut cari cuan! 💸
            </h2>
            <p class="text-xs sm:text-sm font-medium text-slate-300 mt-2 max-w-sm">
                Review lo udah masuk ke database! Feedback lo ngebantu banget biar tools layouting ini makin gacor! 🔥
            </p>
            <div class="mt-4 px-4 py-1.5 bg-white/10 text-white font-mono text-[11px] rounded-full font-semibold border border-white/10 backdrop-blur-sm">
                Menutup otomatis... 💨
            </div>
        </div>

    </div>
</div>

    <div id="toast-container" class="no-print fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4"></div>

    <script>

        const App = {

            currentStep: 1,
            originalImageSrc: null,
            processedImageSrc: null,
            croppedImageSrc: null,
            activeBgColor: 'transparent',
            cropperInstance: null,
            isProcessingAI: false,
            activeBlobUrls: new Set(),
            manualEditTool: 'eraser',
            manualBrushSize: 20,
            manualIsDrawing: false,
            manualLastX: 0,
            manualLastY: 0,
            manualHistory: [],
            manualHistoryIndex: -1,
            manualOriginalImageCanvas: null,
            manualScratchCanvas: null,
            manualScratchCtx: null,
            manualZoom: 1.0,
            manualBaseWidth: 0,
            manualBaseHeight: 0,

            sizesConfig: [

                { id: '2x3', name: '2 x 3 cm', width: 20, height: 28, aspect: 20/28, desc: 'Pas Foto KTP / Buku Nikah', category: 'Pas Foto', defaultQty: 0 },
                { id: '3x4', name: '3 x 4 cm', width: 28, height: 38, aspect: 28/38, desc: 'Pas Foto SKCK / Ijazah / CPNS', category: 'Pas Foto', defaultQty: 0 },
                { id: '4x6', name: '4 x 6 cm', width: 38, height: 56, aspect: 38/56, desc: 'Pas Foto Visa / Lamaran Kerja', category: 'Pas Foto', defaultQty: 0 },

                { id: '2R', name: '2R (6x9 cm)', width: 60, height: 90, aspect: 60/90, desc: 'Foto Dompet / Polaroid', category: 'Seri R', defaultQty: 0 },
                { id: '3R', name: '3R (8.9x12.7)', width: 89, height: 127, aspect: 89/127, desc: 'Cetak Album Standar', category: 'Seri R', defaultQty: 0 },
                { id: '4R', name: '4R (10.2x15.2)', width: 102, height: 152, aspect: 102/152, desc: 'Cetak Paling Populer', category: 'Seri R', defaultQty: 0 },
                { id: '5R', name: '5R (12.7x17.8)', width: 127, height: 178, aspect: 127/178, desc: 'Bingkai Meja Sedang', category: 'Seri R', defaultQty: 0 },
                { id: '6R', name: '6R (15.2x20.3)', width: 152, height: 203, aspect: 152/203, desc: 'Bingkai Foto Keluarga', category: 'Seri R', defaultQty: 0 },
                { id: '8R', name: '8R (20.3x25.4)', width: 203, height: 254, aspect: 203/254, desc: 'Foto Wisuda / Potret Formal', category: 'Seri R', defaultQty: 0 },
                { id: '10R', name: '10R (25.4x30.5)', width: 254, height: 305, aspect: 254/305, desc: 'Bingkai Dinding Standar', category: 'Seri R', defaultQty: 0 },
                { id: '12R', name: '12R (30.5x38.1)', width: 305, height: 381, aspect: 305/381, desc: 'Cetak Poster / Bingkai Besar', category: 'Seri R', defaultQty: 0 }
            ],
            selectedQuantities: {},
            confirmedSizes: new Set(),
            activeEditingSizeId: '3x4',
            selectedCropRatio: 28/38,

            paperSize: 'A4',
            paperWidthMm: 210,
            paperHeightMm: 297,
            paperOrientation: 'portrait',
            canvasZoom: 1.0,
            cutLineThicknessMm: 0.08,
            cutLineOpacity: 25,
            cutLineOffsetMm: 1.5,
            photoGapMm: 3,
            paperMarginMm: 5,
            showCutGuide: true,

            activeFeedbackCategory: 'Saran Fitur',
            feedbackKeydownHandler: null,

            photosOnCanvas: [],
            selectedPhotoIds: new Set(),
            history: [],
            historyIndex: -1,

            filterState: {
                brightness: 100,
                contrast: 100,
                saturate: 100,
                sepia: 0
            },

            init() {

                this.sizesConfig.forEach(s => {
                    this.selectedQuantities[s.id] = 0;
                });

                this.renderSizeCards();
                this.setupDropzone();
                this.setupKeyboardShortcuts();
                this.updateTotalQuantityDisplay();
                this.checkAIModelStatus();

                window.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        const loader = document.getElementById('preloader');
                        if (loader) {
                            loader.classList.add('opacity-0', 'pointer-events-none');
                            setTimeout(() => loader.remove(), 300);
                        }
                    }, 4000);
                });
            },

            goToStep(step) {
                if (step > 1 && !this.originalImageSrc) {
                    this.showToast('Silakan pilih atau unggah foto terlebih dahulu.', 'warning');
                    return;
                }

                this.currentStep = step;

                document.querySelectorAll('.step-view').forEach(el => el.classList.add('hidden'));

                if (step === 1) {
                    document.getElementById('step-upload').classList.remove('hidden');
                } else if (step === 2) {
                    document.getElementById('step-size').classList.remove('hidden');
                    this.initCropperIfNeeded();
                } else if (step === 3) {
                    document.getElementById('step-print').classList.remove('hidden');
                    this.preparePrintCanvas();
                }

                for (let i = 1; i <= 3; i++) {
                    const btn = document.getElementById(`nav-step-${i}`);
                    if (btn) {
                        const num = btn.querySelector('span:first-child');
                        if (i === step) {
                            btn.className = 'nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm';
                            if (num) num.className = 'size-5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[11px] font-bold flex items-center justify-center';
                        } else if (i < step) {
                            btn.className = 'nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 text-brand-600 dark:text-brand-400 hover:text-brand-700';
                            if (num) num.className = 'size-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold flex items-center justify-center';
                        } else {
                            btn.className = 'nav-step-btn flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                            if (num) num.className = 'size-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold flex items-center justify-center';
                        }
                    }
                }

                const mobileTitle = document.getElementById('mobile-step-title');
                if (mobileTitle) {
                    mobileTitle.textContent = `Langkah ${step}/3`;
                }

                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            triggerAddAnotherPhoto() {
                const input = document.getElementById('photo-upload');
                if (input) {
                    input.value = '';
                    input.click();
                }
            },

            switchMobileEditTab(tabName) {
                const tools = document.getElementById('panel-tools');
                const cropper = document.getElementById('panel-cropper');
                const sizes = document.getElementById('panel-sizes');

                const btnTools = document.getElementById('tab-btn-tools');
                const btnCropper = document.getElementById('tab-btn-cropper');
                const btnSizes = document.getElementById('tab-btn-sizes');

                [tools, cropper, sizes].forEach(p => p.classList.add('hidden'));
                [btnTools, btnCropper, btnSizes].forEach(b => {
                    b.className = 'flex-1 py-2 px-3 rounded-lg text-slate-600 dark:text-slate-400 text-center transition-all flex items-center justify-center gap-1.5';
                });

                const activeBtnClass = 'flex-1 py-2 px-3 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm text-center transition-all flex items-center justify-center gap-1.5';

                if (tabName === 'tools') {
                    tools.classList.remove('hidden');
                    btnTools.className = activeBtnClass;
                } else if (tabName === 'cropper') {
                    cropper.classList.remove('hidden');
                    btnCropper.className = activeBtnClass;
                } else if (tabName === 'sizes') {
                    sizes.classList.remove('hidden');
                    btnSizes.className = activeBtnClass;
                }
            },

            setupDropzone() {
                const dropZone = document.getElementById('drop-zone');
                if (!dropZone) return;

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.add('border-slate-900', 'dark:border-white', 'scale-[1.01]');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.remove('border-slate-900', 'dark:border-white', 'scale-[1.01]');
                    }, false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length > 0) {
                        this.processUploadedFile(files[0]);
                    }
                }, false);
            },

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processUploadedFile(file);
                }
            },

            async processUploadedFile(file) {
                if (!file) return;

                const isHeic = file.type === 'image/heic' || file.type === 'image/heif' ||
                               (file.name && (file.name.toLowerCase().endsWith('.heic') || file.name.toLowerCase().endsWith('.heif')));
                const isStandardImg = (file.type && file.type.match(/^image\/(jpeg|png|webp|bmp|gif)$/i)) ||
                                      /\.(jpe?g|png|webp|heic|heif|bmp|gif)$/i.test(file.name || '');

                if (!isHeic && !isStandardImg) {
                    this.showToast('Format berkas tidak valid. Harap pilih gambar JPG, PNG, WEBP, atau iPhone HEIC.', 'error');
                    return;
                }

                if (file.size > 50 * 1024 * 1024) {
                    this.showToast('Ukuran berkas melebihi batas 50MB.', 'error');
                    return;
                }

                let processFile = file;
                if (isHeic && typeof heic2any === 'function') {
                    this.showToast('Mengonversi foto iPhone (HEIC)...', 'info');
                    try {
                        const converted = await heic2any({
                            blob: file,
                            toType: 'image/jpeg',
                            quality: 1.0
                        });
                        processFile = Array.isArray(converted) ? converted[0] : converted;
                    } catch (heicErr) {
                        console.warn('Gagal konversi otomatis HEIC:', heicErr);
                    }
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.revokeAllObjectUrls();
                    this.originalImageSrc = e.target.result;
                    this.processedImageSrc = this.originalImageSrc;
                    this.resetEnhancements(false);
                    this.sizesConfig.forEach(s => {
                        this.selectedQuantities[s.id] = 0;
                    });
                    this.confirmedSizes.clear();
                    this.activeEditingSizeId = '3x4';
                    this.renderSizeCards();
                    this.updateTotalQuantityDisplay();
                    this.showToast('Foto berhasil dimuat!', 'success');
                    this.goToStep(2);
                };
                reader.readAsDataURL(processFile);
            },

            loadSamplePhoto() {
                this.revokeAllObjectUrls();
                const canvas = document.createElement('canvas');
                canvas.width = 1200;
                canvas.height = 1600;
                const ctx = canvas.getContext('2d');

                const grad = ctx.createLinearGradient(0, 0, 0, 1600);
                grad.addColorStop(0, '#e2e8f0');
                grad.addColorStop(1, '#cbd5e1');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 1200, 1600);

                ctx.fillStyle = '#1e293b';
                ctx.beginPath();
                ctx.arc(600, 1500, 450, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#f8fafc';
                ctx.beginPath();
                ctx.arc(600, 650, 220, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#334155';
                ctx.font = 'bold 44px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('CONTOH PAS FOTO', 600, 660);
                ctx.font = '32px Inter, sans-serif';
                ctx.fillText('Turikale Print Studio', 600, 720);

                this.originalImageSrc = canvas.toDataURL('image/png');
                this.processedImageSrc = this.originalImageSrc;

                this.resetEnhancements(false);

                this.sizesConfig.forEach(s => {
                    this.selectedQuantities[s.id] = 0;
                });
                this.confirmedSizes.clear();
                this.activeEditingSizeId = '3x4';
                this.renderSizeCards();
                this.updateTotalQuantityDisplay();

                this.showToast('Contoh foto berhasil dimuat.', 'info');
                this.goToStep(2);
            },

            initCropperIfNeeded() {
                const imgEl = document.getElementById('cropper-image');
                if (!imgEl || !this.processedImageSrc) return;

                imgEl.src = this.processedImageSrc;
                imgEl.classList.remove('hidden');

                if (this.cropperInstance) {
                    this.cropperInstance.destroy();
                }

                this.cropperInstance = new Cropper(imgEl, {
                    aspectRatio: this.selectedCropRatio,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.88,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    responsive: true,
                    zoomOnTouch: true,
                    wheelZoomRatio: 0.1,
                    ready: () => {
                        this.applyCurrentFiltersToCropper();

                        const cropperContainer = imgEl.parentElement ? imgEl.parentElement.querySelector('.cropper-container') : null;
                        if (cropperContainer) {
                            cropperContainer.classList.add('touch-none', 'select-none');
                            cropperContainer.addEventListener('pointerdown', (e) => {
                                e.stopPropagation();
                            }, { passive: true });
                            cropperContainer.addEventListener('touchmove', (e) => {
                                if (e.cancelable) e.preventDefault();
                            }, { passive: false });
                        }
                    }
                });
            },

            selectSizeForCrop(sizeId) {
                const target = this.sizesConfig.find(s => s.id === sizeId);
                if (!target) return;

                this.activeEditingSizeId = sizeId;
                this.setCropRatio(target.aspect, target.name);
                this.renderSizeCards();

                const btn = document.getElementById('btn-confirm-crop');
                if (btn) {
                    btn.classList.add('animate-pulse-confirm');
                }

                this.showToast(`Rasio crop diatur ke ${target.name}. Silakan sesuaikan frame lalu klik 'Konfirmasi Frame'.`, 'info');
            },

            setCropRatio(ratio, labelText) {
                this.selectedCropRatio = ratio;
                if (this.cropperInstance) {
                    this.cropperInstance.setAspectRatio(ratio);
                }
                const label = document.getElementById('cropper-ratio-label');
                if (label) label.textContent = `Rasio: ${labelText}`;
            },

            confirmCurrentFrame() {
                if (!this.activeEditingSizeId) {
                    this.activeEditingSizeId = '3x4';
                }

                const targetSize = this.sizesConfig.find(s => s.id === this.activeEditingSizeId);
                if (!targetSize) return;

                this.confirmedSizes.add(this.activeEditingSizeId);

                if (!this.selectedQuantities[this.activeEditingSizeId] || this.selectedQuantities[this.activeEditingSizeId] === 0) {
                    this.selectedQuantities[this.activeEditingSizeId] = 1;
                }

                this.renderSizeCards();
                this.updateTotalQuantityDisplay();

                const btn = document.getElementById('btn-confirm-crop');
                if (btn) {
                    btn.classList.remove('animate-pulse-confirm');
                }

                this.showToast(`Frame foto ${targetSize.name} berhasil dikonfirmasi! Anda kini dapat mengatur jumlah cetak.`, 'success');
            },

            rotateCropper(degree) {
                if (this.cropperInstance) {
                    this.cropperInstance.rotate(degree);
                }
            },

            flipCropper(dir) {
                if (this.cropperInstance) {
                    const data = this.cropperInstance.getData();
                    if (dir === 'h') {
                        this.cropperInstance.scaleX(data.scaleX === -1 ? 1 : -1);
                    } else {
                        this.cropperInstance.scaleY(data.scaleY === -1 ? 1 : -1);
                    }
                }
            },

            resetCropper() {
                if (this.cropperInstance) {
                    this.cropperInstance.reset();
                }
            },

            selectBgColor(colorHex, btnEl) {
                this.activeBgColor = colorHex;
                document.querySelectorAll('.bg-color-btn').forEach(btn => {
                    btn.classList.remove('border-slate-900', 'dark:border-white');
                    btn.classList.add('border-transparent');
                    const check = btn.querySelector('.material-symbols-outlined');
                    if (check) check.classList.add('hidden');
                });

                if (btnEl) {
                    btnEl.classList.add('border-slate-900', 'dark:border-white');
                    btnEl.classList.remove('border-transparent');
                    const check = btnEl.querySelector('.material-symbols-outlined');
                    if (check) check.classList.remove('hidden');
                }

                this.showToast(`Warna latar diatur ke ${colorHex === 'transparent' ? 'Transparan/Asli' : colorHex}`, 'info');
            },

            handleCustomColor(event) {
                this.selectBgColor(event.target.value, null);
            },

            applyFilter(filterType) {
                const b = document.getElementById('slider-brightness');
                const c = document.getElementById('slider-contrast');
                const s = document.getElementById('slider-saturate');
                const sp = document.getElementById('slider-sepia');

                if (filterType === 'auto') {
                    b.value = 110;
                    c.value = 115;
                    s.value = 112;
                    sp.value = 0;
                    this.showToast('Auto Polish: Kecerahan & kontras ditingkatkan optimal.', 'success');
                } else if (filterType === 'brighten') {
                    b.value = 125;
                    c.value = 105;
                    s.value = 100;
                    sp.value = 0;
                    this.showToast('Filter Cerahkan diterapkan.', 'info');
                } else if (filterType === 'contrast') {
                    b.value = 100;
                    c.value = 135;
                    s.value = 105;
                    sp.value = 0;
                    this.showToast('Filter Kontras Tinggi diterapkan.', 'info');
                } else if (filterType === 'warm') {
                    b.value = 105;
                    c.value = 105;
                    s.value = 115;
                    sp.value = 25;
                    this.showToast('Filter Nada Hangat diterapkan.', 'info');
                } else if (filterType === 'cool') {
                    b.value = 105;
                    c.value = 105;
                    s.value = 90;
                    sp.value = 0;
                    this.showToast('Filter Nada Dingin diterapkan.', 'info');
                } else if (filterType === 'vivid') {
                    b.value = 108;
                    c.value = 120;
                    s.value = 135;
                    sp.value = 0;
                    this.showToast('Filter Tajam & Vivid diterapkan.', 'info');
                } else if (filterType === 'bw') {
                    b.value = 105;
                    c.value = 130;
                    s.value = 0;
                    sp.value = 0;
                    this.showToast('Filter Hitam & Putih diterapkan.', 'info');
                }

                this.handleManualAdjustment();
            },

            handleManualAdjustment() {
                const b = document.getElementById('slider-brightness')?.value || 100;
                const c = document.getElementById('slider-contrast')?.value || 100;
                const s = document.getElementById('slider-saturate')?.value || 100;
                const sp = document.getElementById('slider-sepia')?.value || 0;

                this.filterState = {
                    brightness: parseInt(b, 10),
                    contrast: parseInt(c, 10),
                    saturate: parseInt(s, 10),
                    sepia: parseInt(sp, 10)
                };

                document.getElementById('val-brightness').textContent = `${b}%`;
                document.getElementById('val-contrast').textContent = `${c}%`;
                document.getElementById('val-saturate').textContent = `${s}%`;
                document.getElementById('val-sepia').textContent = `${sp}%`;

                this.applyCurrentFiltersToCropper();
            },

            getFilterString() {
                const { brightness, contrast, saturate, sepia } = this.filterState;
                return `brightness(${brightness}%) contrast(${contrast}%) saturate(${saturate}%) sepia(${sepia}%)`;
            },

            applyCurrentFiltersToCropper() {
                const filterString = this.getFilterString();

                const cropperImage = document.getElementById('cropper-image');
                if (cropperImage) {
                    cropperImage.style.filter = filterString;
                }

                document.querySelectorAll('.cropper-view-box img, .cropper-canvas img, .cropper-container img').forEach(img => {
                    img.style.filter = filterString;
                });
            },

            resetEnhancements(showNotification = true) {
                if (document.getElementById('slider-brightness')) document.getElementById('slider-brightness').value = 100;
                if (document.getElementById('slider-contrast')) document.getElementById('slider-contrast').value = 100;
                if (document.getElementById('slider-saturate')) document.getElementById('slider-saturate').value = 100;
                if (document.getElementById('slider-sepia')) document.getElementById('slider-sepia').value = 0;

                this.handleManualAdjustment();
                if (showNotification) {
                    this.showToast('Semua penyesuaian warna direset ke default.', 'info');
                }
            },

            checkIfSizeExceedsPaper(sizeObj) {
                const pW = this.paperWidthMm;
                const pH = this.paperHeightMm;

                const fitsNormal = sizeObj.width <= pW && sizeObj.height <= pH;
                const fitsRotated = sizeObj.height <= pW && sizeObj.width <= pH;

                return !(fitsNormal || fitsRotated);
            },

            renderSizeCards() {
                const container = document.getElementById('size-cards-container');
                if (!container) return;

                container.innerHTML = '';

                this.sizesConfig.forEach(size => {
                    const qty = this.selectedQuantities[size.id] || 0;
                    const isSelected = qty > 0;
                    const isConfirmed = this.confirmedSizes.has(size.id);
                    const isEditing = this.activeEditingSizeId === size.id;
                    const exceedsPaper = this.checkIfSizeExceedsPaper(size);

                    const card = document.createElement('div');
                    card.className = `p-2 rounded-2xl border transition-all duration-200 flex flex-col justify-between gap-1.5 ${
                        isEditing
                            ? 'ring-2 ring-brand-500 bg-brand-50/60 dark:bg-brand-950/40 border-brand-500 shadow-sm'
                            : (exceedsPaper
                                ? 'bg-amber-50/80 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800'
                                : (isSelected ? 'bg-slate-100 dark:bg-slate-800/90 border-slate-900 dark:border-white shadow-sm' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 hover:border-slate-300'))
                    }`;

                    card.innerHTML = `

                        <div class="cursor-pointer text-center py-1 rounded-xl hover:bg-white/60 dark:hover:bg-slate-700/50 transition-colors" onclick="App.selectSizeForCrop('${size.id}')" title="Klik untuk atur & potong frame ${size.name}">
                            <div class="font-extrabold text-xs text-slate-900 dark:text-white flex items-center justify-center gap-1">
                                ${size.name}
                                ${isConfirmed ? '<span class="material-symbols-outlined text-emerald-600 text-[13px]" title="Frame Telah Dikonfirmasi">check_circle</span>' : ''}
                                ${exceedsPaper ? '<span class="material-symbols-outlined text-amber-600 text-[13px]" title="Lebih besar dari ukuran kertas">warning</span>' : ''}
                            </div>

                        </div>

                        <div class="flex items-center justify-between bg-white dark:bg-slate-900 px-1 py-0.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <button onclick="App.handleQuantityChange('${size.id}', -1)" class="size-5 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition-colors">
                                -
                            </button>
                            <input type="number" min="0" max="99" value="${qty}" onchange="App.handleQuantityInput('${size.id}', this.value)" class="w-6 text-center text-xs font-bold text-slate-900 dark:text-white border-none p-0 focus:ring-0 bg-transparent">
                            <button onclick="App.handleQuantityChange('${size.id}', 1)" class="size-5 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition-colors">
                                +
                            </button>
                        </div>
                    `;

                    container.appendChild(card);
                });
            },

            handleQuantityChange(sizeId, delta) {
                const current = this.selectedQuantities[sizeId] || 0;

                if (delta > 0 && !this.confirmedSizes.has(sizeId) && current === 0) {
                    this.selectSizeForCrop(sizeId);
                    this.showToast('Silakan atur frame pada cropper lalu klik tombol "Konfirmasi Frame".', 'warning');
                    return;
                }

                const next = Math.max(0, current + delta);
                this.setQuantity(sizeId, next);
            },

            handleQuantityInput(sizeId, value) {
                const num = Math.max(0, parseInt(value, 10) || 0);
                if (num > 0 && !this.confirmedSizes.has(sizeId)) {
                    this.selectSizeForCrop(sizeId);
                    this.showToast('Silakan sesuaikan & klik "Konfirmasi Frame" untuk ukuran ini terlebih dahulu.', 'warning');
                    return;
                }
                this.setQuantity(sizeId, num);
            },

            setQuantity(sizeId, value) {
                const num = Math.max(0, parseInt(value, 10) || 0);
                this.selectedQuantities[sizeId] = num;

                if (num > 0) {
                    const targetSize = this.sizesConfig.find(s => s.id === sizeId);
                    if (targetSize && this.checkIfSizeExceedsPaper(targetSize)) {
                        this.showSizeWarningAlert(targetSize);
                    }
                }

                this.renderSizeCards();
                this.updateTotalQuantityDisplay();
            },

            showSizeWarningAlert(sizeObj) {
                const msg = document.getElementById('size-warning-message');
                if (msg) {
                    msg.innerHTML = `Ukuran foto <strong>${sizeObj.name}</strong> (${sizeObj.width} x ${sizeObj.height} mm) melebihi format kertas saat ini <strong>${this.paperSize}</strong> (${this.paperWidthMm} x ${this.paperHeightMm} mm).`;
                }
                this.toggleSizeWarningModal(true);
            },

            toggleSizeWarningModal(show) {
                const modal = document.getElementById('modal-size-warning');
                if (!modal) return;
                if (show) {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
            },

            toggleSizeGuideModal(show) {
                const modal = document.getElementById('modal-size-guide');
                if (!modal) return;
                if (show) {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
            },

            autoSwitchToLargerPaper() {
                const select = document.getElementById('paper-size-select');
                if (select) {
                    select.value = 'A3';
                    this.handlePaperSizeChange();
                }
                this.toggleSizeWarningModal(false);
                this.renderSizeCards();
                this.showToast('Format kertas diubah ke Kertas A3 (297 x 420 mm).', 'success');
            },

            updateTotalQuantityDisplay() {
                let total = 0;
                Object.values(this.selectedQuantities).forEach(q => total += q);
                const label = document.getElementById('total-qty-label');
                if (label) label.textContent = `${total} Foto`;
            },

            async getFinalCroppedImage() {
                if (!this.cropperInstance) return null;

                const croppedCanvas = this.cropperInstance.getCroppedCanvas({
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                    maxWidth: 4096,
                    maxHeight: 4096,
                });

                if (!croppedCanvas) return null;

                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = croppedCanvas.width;
                finalCanvas.height = croppedCanvas.height;
                const ctx = finalCanvas.getContext('2d');

                if (this.activeBgColor && this.activeBgColor !== 'transparent') {
                    ctx.fillStyle = this.activeBgColor;
                    ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
                }

                ctx.filter = this.getFilterString();

                ctx.drawImage(croppedCanvas, 0, 0);

                return finalCanvas.toDataURL('image/png', 1.0);
            },

            async confirmAndGoToPrint() {
                let total = 0;
                Object.values(this.selectedQuantities).forEach(q => total += q);

                if (total === 0) {
                    this.showToast('Silakan pilih ukuran, konfirmasi frame, dan atur jumlah foto yang ingin dicetak terlebih dahulu.', 'warning');
                    return;
                }

                const finalDataUrl = await this.getFinalCroppedImage();
                if (!finalDataUrl) {
                    this.showToast('Gagal memproses crop foto.', 'error');
                    return;
                }

                this.croppedImageSrc = finalDataUrl;

                this.appendCanvasPhotosFromQuantities(finalDataUrl);

                this.goToStep(3);
            },

            preparePrintCanvas() {
                this.updatePaperDimensions();
                this.autoArrangePhotos(false);
            },

            updatePaperDimensions() {
                const select = document.getElementById('paper-size-select');
                const selectedOption = select ? select.options[select.selectedIndex] : null;

                let w = selectedOption ? parseFloat(selectedOption.getAttribute('data-w')) : 210;
                let h = selectedOption ? parseFloat(selectedOption.getAttribute('data-h')) : 297;

                if (this.paperOrientation === 'landscape') {
                    const temp = w;
                    w = h;
                    h = temp;
                }

                this.paperWidthMm = w;
                this.paperHeightMm = h;

                const canvasEl = document.getElementById('paper-canvas');
                if (canvasEl) {
                    canvasEl.style.width = `${w}mm`;
                    canvasEl.style.height = `${h}mm`;
                }

                const badge = document.getElementById('current-paper-badge');
                if (badge) {
                    badge.textContent = `${select ? select.value : 'A4'} (${w} x ${h} mm)`;
                }

                this.fitCanvasToScreen();
            },

            setPaperOrientation(orient) {
                this.paperOrientation = orient;
                const btnP = document.getElementById('btn-orient-p');
                const btnL = document.getElementById('btn-orient-l');

                if (orient === 'portrait') {
                    btnP.className = 'p-1.5 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition-all text-xs font-semibold flex items-center gap-1';
                    btnL.className = 'p-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs font-semibold flex items-center gap-1';
                } else {
                    btnL.className = 'p-1.5 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition-all text-xs font-semibold flex items-center gap-1';
                    btnP.className = 'p-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs font-semibold flex items-center gap-1';
                }

                this.updatePaperDimensions();
                this.autoArrangePhotos(false);
            },

            handlePaperSizeChange() {
                this.paperSize = document.getElementById('paper-size-select').value;
                this.updatePaperDimensions();
                this.renderSizeCards();
                this.autoArrangePhotos(false);
            },

            zoomCanvas(delta) {
                this.canvasZoom = Math.min(2.5, Math.max(0.2, this.canvasZoom + delta));
                this.applyCanvasZoom();
            },

            fitCanvasToScreen() {
                const viewport = document.getElementById('paper-viewport');
                if (!viewport) return;

                const vW = viewport.clientWidth - 80;
                const vH = viewport.clientHeight - 80;

                const pWPx = this.paperWidthMm * 3.7795;
                const pHPx = this.paperHeightMm * 3.7795;

                const scaleW = vW / pWPx;
                const scaleH = vH / pHPx;
                this.canvasZoom = Math.min(scaleW, scaleH, 1.0);
                this.applyCanvasZoom();
            },

            applyCanvasZoom() {
                const workspace = document.getElementById('paper-workspace');
                const text = document.getElementById('zoom-level-text');
                if (workspace) {
                    workspace.style.transform = `scale(${this.canvasZoom})`;
                }
                if (text) {
                    text.textContent = `${Math.round(this.canvasZoom * 100)}%`;
                }
                this.updateFloatingToolbar();
            },

            appendCanvasPhotosFromQuantities(imageSrc) {
                let counter = this.photosOnCanvas.length + 1;

                this.sizesConfig.forEach(size => {
                    const qty = this.selectedQuantities[size.id] || 0;
                    for (let i = 0; i < qty; i++) {
                        this.photosOnCanvas.push({
                            id: `photo-${counter++}-${Date.now()}`,
                            sizeId: size.id,
                            name: size.name,
                            widthMm: size.width,
                            heightMm: size.height,
                            xMm: 0,
                            yMm: 0,
                            rotation: 0,
                            imageSrc: imageSrc
                        });
                    }
                });

                this.sizesConfig.forEach(s => {
                    this.selectedQuantities[s.id] = 0;
                });
                this.confirmedSizes.clear();
                this.renderSizeCards();
                this.updateTotalQuantityDisplay();

                this.recordHistory();
            },

            autoArrangePhotos(showToastMessage = true) {
                if (this.photosOnCanvas.length === 0) {
                    this.renderCanvasPhotos();
                    this.renderCutMarks();
                    return;
                }

                const margin = this.paperMarginMm;
                const gap = this.photoGapMm;
                const availW = this.paperWidthMm - (margin * 2);
                const availH = this.paperHeightMm - (margin * 2);

                const sorted = [...this.photosOnCanvas].sort((a, b) => (b.heightMm * b.widthMm) - (a.heightMm * a.widthMm));

                let curX = margin;
                let curY = margin;
                let rowHeight = 0;

                sorted.forEach(item => {

                    if (curX + item.widthMm > margin + availW + 0.01) {

                        curX = margin;
                        curY += rowHeight + gap;
                        rowHeight = 0;
                    }

                    item.xMm = curX;
                    item.yMm = curY;

                    rowHeight = Math.max(rowHeight, item.heightMm);
                    curX += item.widthMm + gap;
                });

                this.renderCanvasPhotos();
                this.renderCutMarks();
                this.updateFloatingToolbar();

                if (showToastMessage) {
                    this.recordHistory();
                    this.showToast('Tata letak otomatis dirapikan!', 'info');
                }
            },

            renderCanvasPhotos() {
                const layer = document.getElementById('canvas-photos-layer');
                if (!layer) return;

                layer.innerHTML = '';

                this.photosOnCanvas.forEach(photo => {
                    const el = document.createElement('div');
                    el.id = photo.id;
                    el.className = `canvas-photo-item touch-none select-none ${this.selectedPhotoIds.has(photo.id) ? 'is-selected' : ''}`;

                    el.style.left = `${photo.xMm}mm`;
                    el.style.top = `${photo.yMm}mm`;
                    el.style.width = `${photo.widthMm}mm`;
                    el.style.height = `${photo.heightMm}mm`;

                    let imgStyle = 'width: 100%; height: 100%; object-fit: cover;';
                    if (photo.rotation % 180 !== 0) {
                        imgStyle = `width: ${photo.heightMm}mm; height: ${photo.widthMm}mm; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%) rotate(${photo.rotation}deg); object-fit: cover;`;
                    } else if (photo.rotation !== 0) {
                        imgStyle = `width: 100%; height: 100%; transform: rotate(${photo.rotation}deg); object-fit: cover;`;
                    }

                    el.innerHTML = `
                        <div class="photo-bleed-wrapper w-full h-full relative bg-white overflow-hidden flex items-center justify-center pointer-events-none select-none">
                            <img src="${photo.imageSrc}" class="pointer-events-none select-none" style="${imgStyle}" alt="Foto">
                            <div class="absolute inset-0 bg-slate-900/10 opacity-0 hover:opacity-100 transition-opacity pointer-events-none"></div>
                        </div>
                    `;

                    this.attachPointerDrag(el, photo);

                    layer.appendChild(el);
                });

                const countBadge = document.getElementById('canvas-photo-count-badge');
                if (countBadge) {
                    countBadge.textContent = `${this.photosOnCanvas.length} Foto di Lembar`;
                }
            },

            renderCutMarks() {
                const container = document.getElementById('cut-marks-container');
                if (!container) return;

                container.innerHTML = '';
                if (!this.showCutGuide) return;

                const offset = this.cutLineOffsetMm;
                const opacity = (this.cutLineOpacity / 100).toFixed(2);

                this.photosOnCanvas.forEach(photo => {
                    const x = photo.xMm;
                    const y = photo.yMm;
                    const w = photo.widthMm;
                    const h = photo.heightMm;

                    const boxEl = document.createElement('div');
                    boxEl.id = `cut-mark-${photo.id}`;
                    boxEl.className = 'cut-mark-dashed-box border-dashed border-[0.5px] pointer-events-none';
                    boxEl.style.boxSizing = 'border-box';
                    boxEl.style.left = `${x - offset}mm`;
                    boxEl.style.top = `${y - offset}mm`;
                    boxEl.style.width = `${w + (offset * 2)}mm`;
                    boxEl.style.height = `${h + (offset * 2)}mm`;
                    boxEl.style.borderWidth = '0.5px';
                    boxEl.style.borderStyle = 'dashed';
                    boxEl.style.borderColor = '#64748b';
                    boxEl.style.opacity = opacity;

                    boxEl.innerHTML = `
                        <div class="crop-mark-corner cm-tl"></div>
                        <div class="crop-mark-corner cm-tr"></div>
                        <div class="crop-mark-corner cm-bl"></div>
                        <div class="crop-mark-corner cm-br"></div>
                    `;

                    container.appendChild(boxEl);
                });
            },

            updateCutGuideSettings(source = 'all') {
                const toggleEl = document.getElementById('cut-guide-toggle');
                const opacityEl = document.getElementById('slider-cut-opacity');
                const offsetEl = document.getElementById('slider-cut-offset');
                const gapEl = document.getElementById('slider-photo-gap');
                const marginEl = document.getElementById('slider-paper-margin');

                if (toggleEl) this.showCutGuide = toggleEl.checked;
                if (opacityEl) this.cutLineOpacity = parseInt(opacityEl.value, 10) || 25;
                if (offsetEl) this.cutLineOffsetMm = parseFloat(offsetEl.value) || 1.5;
                if (gapEl) this.photoGapMm = parseFloat(gapEl.value) || 3;
                if (marginEl) this.paperMarginMm = parseFloat(marginEl.value) || 5;

                const lblOpacity = document.getElementById('label-cut-opacity');
                const lblOffset = document.getElementById('label-cut-offset');
                const lblGap = document.getElementById('label-photo-gap');
                const lblMargin = document.getElementById('label-paper-margin');

                if (lblOpacity) lblOpacity.textContent = `${this.cutLineOpacity}%`;
                if (lblOffset) lblOffset.textContent = `${this.cutLineOffsetMm} mm`;
                if (lblGap) lblGap.textContent = `${this.photoGapMm} mm`;
                if (lblMargin) lblMargin.textContent = `${this.paperMarginMm} mm`;

                const container = document.getElementById('cut-marks-container');
                if (container) {
                    container.style.display = this.showCutGuide ? 'block' : 'none';
                }

                if (!this.showCutGuide) return;

                if (source === 'opacity') {
                    const opacityVal = (this.cutLineOpacity / 100).toFixed(2);
                    const cutBoxes = document.querySelectorAll('.cut-mark-dashed-box');
                    cutBoxes.forEach(b => {
                        b.style.opacity = opacityVal;
                    });
                    return;
                }

                if (source === 'offset') {
                    this.renderCutMarks();
                    return;
                }

                this.autoArrangePhotos(false);
            },

            updateFloatingToolbar() {
                const toolbar = document.getElementById('floating-photo-toolbar');
                if (!toolbar) return;

                if (this.selectedPhotoIds.size === 0) {
                    toolbar.classList.add('hidden');
                    return;
                }

                const selectedPhotos = this.photosOnCanvas.filter(p => this.selectedPhotoIds.has(p.id));
                if (selectedPhotos.length === 0) {
                    toolbar.classList.add('hidden');
                    return;
                }

                const target = selectedPhotos[0];
                const domEl = document.getElementById(target.id);
                if (!domEl) {
                    toolbar.classList.add('hidden');
                    return;
                }

                const centerX = target.xMm + (target.widthMm / 2);
                const clampedX = Math.max(16, Math.min(this.paperWidthMm - 16, centerX));
                toolbar.style.left = `${clampedX}mm`;

                if (target.yMm < 16) {
                    toolbar.style.top = `${target.yMm + target.heightMm + 2.5}mm`;
                    toolbar.className = 'no-print absolute z-50 bg-slate-900/95 text-white backdrop-blur-md px-1.5 py-1 rounded-xl border border-white/20 shadow-xl flex items-center gap-1 -translate-x-1/2 pointer-events-auto';
                } else {
                    toolbar.style.top = `${target.yMm - 2.5}mm`;
                    toolbar.className = 'no-print absolute z-50 bg-slate-900/95 text-white backdrop-blur-md px-1.5 py-1 rounded-xl border border-white/20 shadow-xl flex items-center gap-1 -translate-x-1/2 -translate-y-full pointer-events-auto';
                }

                toolbar.classList.remove('hidden');
            },

            renderSelectionVisuals() {
                this.photosOnCanvas.forEach(p => {
                    const domEl = document.getElementById(p.id);
                    if (domEl) {
                        if (this.selectedPhotoIds.has(p.id)) {
                            domEl.classList.add('is-selected');
                        } else {
                            domEl.classList.remove('is-selected');
                        }
                    }
                });
            },

            rotateSelectedPhoto(deltaDeg) {
                if (this.selectedPhotoIds.size === 0) {
                    this.showToast('Pilih foto terlebih dahulu untuk diputar.', 'warning');
                    return;
                }

                this.photosOnCanvas.forEach(photo => {
                    if (this.selectedPhotoIds.has(photo.id)) {

                        const oldW = photo.widthMm;
                        const oldH = photo.heightMm;
                        photo.widthMm = oldH;
                        photo.heightMm = oldW;

                        photo.rotation = (photo.rotation + deltaDeg + 360) % 360;

                        photo.xMm = Math.max(0, Math.min(this.paperWidthMm - photo.widthMm, photo.xMm));
                        photo.yMm = Math.max(0, Math.min(this.paperHeightMm - photo.heightMm, photo.yMm));
                    }
                });

                this.renderCanvasPhotos();
                this.renderCutMarks();
                this.updateFloatingToolbar();
                this.recordHistory();
                this.showToast(`Foto diputar ${deltaDeg > 0 ? '+90° ke kanan' : '-90° ke kiri'}.`, 'info');
            },

            attachPointerDrag(el, photo) {
                let startX = 0;
                let startY = 0;
                let origPositions = [];
                let isDragging = false;
                let rafId = null;
                let lastPointerEvent = null;

                const onPointerDown = (e) => {
                    if (e.pointerType === 'mouse' && e.button !== 0) return;

                    if (e.cancelable) e.preventDefault();
                    e.stopPropagation();

                    if (e.shiftKey || e.ctrlKey || e.metaKey) {
                        if (this.selectedPhotoIds.has(photo.id)) {
                            this.selectedPhotoIds.delete(photo.id);
                        } else {
                            this.selectedPhotoIds.add(photo.id);
                        }
                    } else {
                        if (!this.selectedPhotoIds.has(photo.id)) {
                            this.selectedPhotoIds.clear();
                            this.selectedPhotoIds.add(photo.id);
                        }
                    }

                    this.renderSelectionVisuals();

                    const toolbar = document.getElementById('floating-photo-toolbar');
                    if (toolbar) toolbar.classList.add('hidden');

                    startX = e.clientX;
                    startY = e.clientY;

                    origPositions = this.photosOnCanvas
                        .filter(p => this.selectedPhotoIds.has(p.id))
                        .map(p => ({ id: p.id, xMm: p.xMm, yMm: p.yMm }));

                    isDragging = true;
                    try {
                        el.setPointerCapture(e.pointerId);
                    } catch (err) {}

                    el.addEventListener('pointermove', onPointerMove);
                    el.addEventListener('pointerup', onPointerUp);
                    el.addEventListener('pointercancel', onPointerUp);
                };

                const updatePositions = () => {
                    if (!isDragging || !lastPointerEvent) return;

                    const deltaXPixels = lastPointerEvent.clientX - startX;
                    const deltaYPixels = lastPointerEvent.clientY - startY;

                    const deltaXMm = (deltaXPixels / (3.7795 * this.canvasZoom));
                    const deltaYMm = (deltaYPixels / (3.7795 * this.canvasZoom));

                    origPositions.forEach(orig => {
                        const target = this.photosOnCanvas.find(p => p.id === orig.id);
                        if (target) {
                            target.xMm = Math.max(0, Math.min(this.paperWidthMm - target.widthMm, orig.xMm + deltaXMm));
                            target.yMm = Math.max(0, Math.min(this.paperHeightMm - target.heightMm, orig.yMm + deltaYMm));

                            const domEl = document.getElementById(target.id);
                            if (domEl) {
                                domEl.style.left = `${target.xMm}mm`;
                                domEl.style.top = `${target.yMm}mm`;
                            }

                            const cutEl = document.getElementById(`cut-mark-${target.id}`);
                            if (cutEl) {
                                cutEl.style.left = `${target.xMm - this.cutLineOffsetMm}mm`;
                                cutEl.style.top = `${target.yMm - this.cutLineOffsetMm}mm`;
                            }
                        }
                    });

                    this.checkMagneticSnap(photo);
                    rafId = null;
                };

                const onPointerMove = (e) => {
                    if (!isDragging) return;
                    if (e.cancelable) e.preventDefault();
                    lastPointerEvent = e;

                    if (!rafId) {
                        rafId = requestAnimationFrame(updatePositions);
                    }
                };

                const onPointerUp = (e) => {
                    if (!isDragging) return;
                    isDragging = false;

                    if (rafId) {
                        cancelAnimationFrame(rafId);
                        rafId = null;
                    }

                    try {
                        if (el.hasPointerCapture(e.pointerId)) {
                            el.releasePointerCapture(e.pointerId);
                        }
                    } catch (err) {}

                    el.removeEventListener('pointermove', onPointerMove);
                    el.removeEventListener('pointerup', onPointerUp);
                    el.removeEventListener('pointercancel', onPointerUp);

                    this.hideSnapGuides();
                    this.renderCutMarks();
                    this.updateFloatingToolbar();
                    this.recordHistory();
                };

                el.addEventListener('pointerdown', onPointerDown);
            },

            checkMagneticSnap(activePhoto) {
                const threshold = 1.5;
                let snapX = null;
                let snapY = null;

                const guideV = document.getElementById('snap-guide-v');
                const guideH = document.getElementById('snap-guide-h');

                const paperMidX = this.paperWidthMm / 2;
                const paperMidY = this.paperHeightMm / 2;

                if (Math.abs(activePhoto.xMm - this.paperMarginMm) < threshold) {
                    snapX = this.paperMarginMm;
                } else if (Math.abs(activePhoto.xMm + activePhoto.widthMm / 2 - paperMidX) < threshold) {
                    snapX = paperMidX - activePhoto.widthMm / 2;
                }

                if (Math.abs(activePhoto.yMm - this.paperMarginMm) < threshold) {
                    snapY = this.paperMarginMm;
                } else if (Math.abs(activePhoto.yMm + activePhoto.heightMm / 2 - paperMidY) < threshold) {
                    snapY = paperMidY - activePhoto.heightMm / 2;
                }

                this.photosOnCanvas.forEach(other => {
                    if (other.id === activePhoto.id) return;

                    if (Math.abs(activePhoto.xMm - (other.xMm + other.widthMm + this.photoGapMm)) < threshold) {
                        snapX = other.xMm + other.widthMm + this.photoGapMm;
                    }
                    if (Math.abs(activePhoto.yMm - other.yMm) < threshold) {
                        snapY = other.yMm;
                    }
                });

                if (snapX !== null) {
                    activePhoto.xMm = snapX;
                    const domEl = document.getElementById(activePhoto.id);
                    if (domEl) domEl.style.left = `${snapX}mm`;
                    if (guideV) {
                        guideV.style.left = `${snapX}mm`;
                        guideV.classList.remove('hidden');
                    }
                } else if (guideV) {
                    guideV.classList.add('hidden');
                }

                if (snapY !== null) {
                    activePhoto.yMm = snapY;
                    const domEl = document.getElementById(activePhoto.id);
                    if (domEl) domEl.style.top = `${snapY}mm`;
                    if (guideH) {
                        guideH.style.top = `${snapY}mm`;
                        guideH.classList.remove('hidden');
                    }
                } else if (guideH) {
                    guideH.classList.add('hidden');
                }
            },

            hideSnapGuides() {
                const gV = document.getElementById('snap-guide-v');
                const gH = document.getElementById('snap-guide-h');
                if (gV) gV.classList.add('hidden');
                if (gH) gH.classList.add('hidden');
            },

            handleViewportMouseDown(e) {
                if (e.target.closest('.canvas-photo-item') || e.target.closest('#floating-photo-toolbar')) return;

                this.selectedPhotoIds.clear();
                this.renderSelectionVisuals();
                this.updateFloatingToolbar();

                const paper = document.getElementById('paper-canvas');
                const marquee = document.getElementById('marquee-box');
                if (!paper || !marquee) return;

                const paperRect = paper.getBoundingClientRect();
                const startX = (e.clientX - paperRect.left) / this.canvasZoom;
                const startY = (e.clientY - paperRect.top) / this.canvasZoom;

                marquee.style.left = `${startX}px`;
                marquee.style.top = `${startY}px`;
                marquee.style.width = '0px';
                marquee.style.height = '0px';
                marquee.classList.remove('hidden');

                const onMouseMove = (moveEvent) => {
                    const currentX = (moveEvent.clientX - paperRect.left) / this.canvasZoom;
                    const currentY = (moveEvent.clientY - paperRect.top) / this.canvasZoom;

                    const left = Math.min(startX, currentX);
                    const top = Math.min(startY, currentY);
                    const width = Math.abs(currentX - startX);
                    const height = Math.abs(currentY - startY);

                    marquee.style.left = `${left}px`;
                    marquee.style.top = `${top}px`;
                    marquee.style.width = `${width}px`;
                    marquee.style.height = `${height}px`;

                    this.photosOnCanvas.forEach(photo => {
                        const px = photo.xMm * 3.7795;
                        const py = photo.yMm * 3.7795;
                        const pw = photo.widthMm * 3.7795;
                        const ph = photo.heightMm * 3.7795;

                        const isIntersecting = !(px + pw < left || px > left + width || py + ph < top || py > top + height);
                        if (isIntersecting) {
                            this.selectedPhotoIds.add(photo.id);
                        } else {
                            this.selectedPhotoIds.delete(photo.id);
                        }
                    });

                    this.renderSelectionVisuals();
                    this.updateFloatingToolbar();
                };

                const onMouseUp = () => {
                    marquee.classList.add('hidden');
                    this.updateFloatingToolbar();
                    window.removeEventListener('mousemove', onMouseMove);
                    window.removeEventListener('mouseup', onMouseUp);
                };

                window.addEventListener('mousemove', onMouseMove);
                window.addEventListener('mouseup', onMouseUp);
            },

            renderSelectionVisuals() {
                document.querySelectorAll('.canvas-photo-item').forEach(el => {
                    if (this.selectedPhotoIds.has(el.id)) {
                        el.classList.add('is-selected');
                    } else {
                        el.classList.remove('is-selected');
                    }
                });
            },

            selectAllPhotos() {
                this.photosOnCanvas.forEach(p => this.selectedPhotoIds.add(p.id));
                this.renderSelectionVisuals();
                this.updateFloatingToolbar();
                this.showToast('Semua foto di kanvas terseleksi.', 'info');
            },

            duplicateSelectedPhotos() {
                if (this.selectedStripIds.size === 0) {
                    this.showToast('Pilih foto di kanvas terlebih dahulu.', 'warning');
                    return;
                }

                const newStrips = [];
                const newSelectedIds = new Set();

                this.stripsOnCanvas.forEach(strip => {
                    if (this.selectedStripIds.has(strip.id)) {
                        const newId = `strip-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`;
                        const clone = {
                            ...strip,
                            id: newId,
                            xMm: Math.min(this.paperWidthMm - strip.widthMm, strip.xMm + 5),
                            yMm: Math.min(this.paperHeightMm - strip.heightMm, strip.yMm + 5)
                        };
                        newStrips.push(clone);
                        newSelectedIds.add(newId);
                    }
                });

                this.stripsOnCanvas = [...this.stripsOnCanvas, ...newStrips];
                this.selectedStripIds = newSelectedIds;
                this.renderCanvasPages();
                this.showToast(`${newStrips.length} strip diduplikasi.`, 'success');
            },

            deleteSelectedPhotos() {
                if (this.selectedPhotoIds.size === 0) return;
                this.photosOnCanvas = this.photosOnCanvas.filter(p => !this.selectedPhotoIds.has(p.id));
                this.selectedPhotoIds.clear();
                this.renderCanvasPhotos();
                this.renderCutMarks();
                this.updateFloatingToolbar();
                this.recordHistory();
                this.showToast('Foto terpilih dihapus.', 'info');
            },

            duplicateSelectedPhotos() {
                if (this.selectedPhotoIds.size === 0) return;
                const newPhotos = [];
                this.photosOnCanvas.forEach(p => {
                    if (this.selectedPhotoIds.has(p.id)) {
                        newPhotos.push({
                            ...p,
                            id: `photo-${Date.now()}-${Math.random()}`,
                            xMm: Math.min(this.paperWidthMm - p.widthMm, p.xMm + 4),
                            yMm: Math.min(this.paperHeightMm - p.heightMm, p.yMm + 4)
                        });
                    }
                });

                this.photosOnCanvas = [...this.photosOnCanvas, ...newPhotos];
                this.renderCanvasPhotos();
                this.renderCutMarks();
                this.updateFloatingToolbar();
                this.recordHistory();
                this.showToast(`${newPhotos.length} foto diduplikasi.`, 'success');
            },

            recordHistory() {

                if (this.historyIndex < this.history.length - 1) {
                    this.history = this.history.slice(0, this.historyIndex + 1);
                }
                this.history.push(JSON.stringify(this.photosOnCanvas));
                if (this.history.length > 20) this.history.shift();
                this.historyIndex = this.history.length - 1;
            },

            undo() {
                if (this.historyIndex > 0) {
                    this.historyIndex--;
                    this.photosOnCanvas = JSON.parse(this.history[this.historyIndex]);
                    this.renderCanvasPhotos();
                    this.renderCutMarks();
                    this.updateFloatingToolbar();
                    this.showToast('Urungkan (Undo).', 'info');
                }
            },

            redo() {
                if (this.historyIndex < this.history.length - 1) {
                    this.historyIndex++;
                    this.photosOnCanvas = JSON.parse(this.history[this.historyIndex]);
                    this.renderCanvasPhotos();
                    this.renderCutMarks();
                    this.updateFloatingToolbar();
                    this.showToast('Ulangi (Redo).', 'info');
                }
            },

            setupKeyboardShortcuts() {
                window.addEventListener('keydown', (e) => {

                    if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

                    if (e.key === 'Delete' || e.key === 'Backspace') {
                        if (this.currentStep === 3 && this.selectedPhotoIds.size > 0) {
                            e.preventDefault();
                            this.deleteSelectedPhotos();
                        }
                    }

                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
                        if (this.currentStep === 3) {
                            e.preventDefault();
                            this.selectAllPhotos();
                        }
                    }

                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
                        if (this.currentStep === 3 && this.selectedPhotoIds.size > 0) {
                            e.preventDefault();
                            this.duplicateSelectedPhotos();
                        }
                    }

                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z' && !e.shiftKey) {
                        if (this.currentStep === 3) {
                            e.preventDefault();
                            this.undo();
                        }
                    }

                    if ((e.ctrlKey || e.metaKey) && (e.key.toLowerCase() === 'y' || (e.shiftKey && e.key.toLowerCase() === 'z'))) {
                        if (this.currentStep === 3) {
                            e.preventDefault();
                            this.redo();
                        }
                    }

                    if (e.key === 'Escape') {
                        this.selectedPhotoIds.clear();
                        this.renderSelectionVisuals();
                        this.updateFloatingToolbar();
                        this.toggleShortcutModal(false);
                        this.toggleSizeWarningModal(false);
                        this.toggleSizeGuideModal(false);
                    }
                });
            },

            executePrint() {
                if (this.photosOnCanvas.length === 0) {
                    this.showToast('Kanvas masih kosong, tidak ada foto untuk dicetak.', 'warning');
                    return;
                }

                this.selectedPhotoIds.clear();
                this.renderSelectionVisuals();
                this.updateFloatingToolbar();

                const savedZoom = this.canvasZoom;
                const workspace = document.getElementById('paper-workspace');
                if (workspace) workspace.style.transform = 'none';

                const restoreZoom = () => {
                    if (workspace) workspace.style.transform = `scale(${savedZoom})`;
                    window.removeEventListener('afterprint', restoreZoom);

                    setTimeout(() => {
                if (typeof showGenZEvalModal === 'function') {
                    showGenZEvalModal();
                }
            }, 800);
                };
                window.addEventListener('afterprint', restoreZoom);

                try {
                    confetti({
                        particleCount: 40,
                        spread: 60,
                        origin: { y: 0.8 }
                    });
                } catch(e) {}

                setTimeout(() => {
                    window.print();
                }, 300);
            },

            async exportPaperImage() {
                if (this.photosOnCanvas.length === 0) {
                    this.showToast('Kanvas masih kosong.', 'warning');
                    return;
                }

                this.showToast('Merender lembar cetak resolusi tinggi 300 DPI...', 'info');

                const dpiFactor = 11.811;
                const canvasW = Math.round(this.paperWidthMm * dpiFactor);
                const canvasH = Math.round(this.paperHeightMm * dpiFactor);

                const exportCanvas = document.createElement('canvas');
                exportCanvas.width = canvasW;
                exportCanvas.height = canvasH;
                const ctx = exportCanvas.getContext('2d');

                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvasW, canvasH);

                for (const photo of this.photosOnCanvas) {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.src = photo.imageSrc;

                    await new Promise((resolve) => {
                        img.onload = () => {
                            const px = photo.xMm * dpiFactor;
                            const py = photo.yMm * dpiFactor;
                            const pw = photo.widthMm * dpiFactor;
                            const ph = photo.heightMm * dpiFactor;
                            const off = this.cutLineOffsetMm * dpiFactor;

                            ctx.fillStyle = '#ffffff';
                            ctx.fillRect(px - off, py - off, pw + (off * 2), ph + (off * 2));

                            ctx.save();
                            ctx.translate(px + pw / 2, py + ph / 2);
                            ctx.rotate((photo.rotation * Math.PI) / 180);

                            if (photo.rotation % 180 !== 0) {
                                ctx.drawImage(img, -ph / 2, -pw / 2, ph, pw);
                            } else {
                                ctx.drawImage(img, -pw / 2, -ph / 2, pw, ph);
                            }
                            ctx.restore();

                            if (this.showCutGuide) {
                                ctx.save();
                                const opacity = (this.cutLineOpacity / 100).toFixed(2);
                                const boxX = px - off;
                                const boxY = py - off;
                                const boxW = pw + (off * 2);
                                const boxH = ph + (off * 2);

                                ctx.strokeStyle = `rgba(100, 116, 139, ${opacity})`;
                                ctx.lineWidth = 1;
                                ctx.setLineDash([4, 4]);
                                ctx.strokeRect(boxX, boxY, boxW, boxH);

                                ctx.setLineDash([]);
                                ctx.strokeStyle = `rgba(51, 65, 85, ${opacity})`;
                                ctx.lineWidth = 1;
                                const armLen = Math.round(2.5 * dpiFactor);

                                ctx.beginPath();
                                ctx.moveTo(boxX, boxY + armLen);
                                ctx.lineTo(boxX, boxY);
                                ctx.lineTo(boxX + armLen, boxY);
                                ctx.stroke();

                                ctx.beginPath();
                                ctx.moveTo(boxX + boxW - armLen, boxY);
                                ctx.lineTo(boxX + boxW, boxY);
                                ctx.lineTo(boxX + boxW, boxY + armLen);
                                ctx.stroke();

                                ctx.beginPath();
                                ctx.moveTo(boxX, boxY + boxH - armLen);
                                ctx.lineTo(boxX, boxY + boxH);
                                ctx.lineTo(boxX + armLen, boxY + boxH);
                                ctx.stroke();

                                ctx.beginPath();
                                ctx.moveTo(boxX + boxW - armLen, boxY + boxH);
                                ctx.lineTo(boxX + boxW, boxY + boxH);
                                ctx.lineTo(boxX + boxW, boxY + boxH - armLen);
                                ctx.stroke();

                                ctx.restore();
                            }

                            resolve();
                        };
                        img.onerror = () => resolve();
                    });
                }

                const link = document.createElement('a');
                link.download = `Lembar_Cetak_TurikalePrint_${this.paperSize}_${Date.now()}.png`;
                link.href = exportCanvas.toDataURL('image/png', 1.0);
                link.click();

                this.showToast('Lembar cetak HD 300 DPI berhasil diunduh!', 'success');
            },

            async exportSingleCroppedImage() {
                const dataUrl = await this.getFinalCroppedImage();
                if (!dataUrl) {
                    this.showToast('Gagal memproses foto satuan.', 'error');
                    return;
                }

                const link = document.createElement('a');
                link.download = `PasFoto_TurikalePrint_${Date.now()}.png`;
                link.href = dataUrl;
                link.click();

                this.showToast('Foto satuan HD berhasil diunduh!', 'success');
            },

            createTrackedObjectUrl(blob) {
                const url = URL.createObjectURL(blob);
                this.activeBlobUrls.add(url);
                return url;
            },

            revokeTrackedObjectUrl(url) {
                if (url && typeof url === 'string' && url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                    this.activeBlobUrls.delete(url);
                }
            },

            revokeAllObjectUrls() {
                this.activeBlobUrls.forEach((url) => {
                    try {
                        URL.revokeObjectURL(url);
                    } catch (e) {}
                });
                this.activeBlobUrls.clear();
            },

            async checkAIModelStatus() {
                let isCached = false;
                try {
                    if (localStorage.getItem('imgly_model_cached') === 'true') {
                        isCached = true;
                    }
                } catch (e) {}

                if (!isCached && 'caches' in window) {
                    try {
                        const cacheKeys = await caches.keys();
                        for (const key of cacheKeys) {
                            if (key.toLowerCase().includes('imgly') || key.toLowerCase().includes('onnx') || key.toLowerCase().includes('background-removal')) {
                                const c = await caches.open(key);
                                const reqs = await c.keys();
                                if (reqs && reqs.length > 0) {
                                    isCached = true;
                                    break;
                                }
                            }
                        }
                    } catch (e) {}
                }

                if (!isCached && 'indexedDB' in window && indexedDB.databases) {
                    try {
                        const dbs = await indexedDB.databases();
                        for (const db of dbs) {
                            if (db.name && (db.name.toLowerCase().includes('imgly') || db.name.toLowerCase().includes('onnx'))) {
                                isCached = true;
                                break;
                            }
                        }
                    } catch (e) {}
                }

                this.setAIModelStatusBadge(isCached ? 'ready' : 'not_downloaded');
            },

            aiRemoveFn: null,
            aiModulePromise: null,

            setAIModelStatusBadge(status, pct = 0) {
                const badge = document.getElementById('ai-model-status-badge');
                if (!badge) return;
                if (status === 'ready') {
                    badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300';
                    badge.textContent = 'AI Siap Digunakan';
                    badge.onclick = null;
                    badge.style.cursor = 'default';
                } else if (status === 'downloading') {
                    badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300';
                    badge.textContent = `Mengunduh Model (${pct}%)`;
                    badge.onclick = null;
                    badge.style.cursor = 'default';
                } else {
                    badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors cursor-pointer';
                    badge.textContent = 'AI Belum Diunduh';
                    badge.title = 'Klik untuk mengunduh model AI';
                    badge.onclick = () => App.preloadAIModel();
                }
            },

            showAIInlineProgress(show) {
                const box = document.getElementById('ai-inline-progress');
                if (!box) return;
                if (show) {
                    box.classList.remove('hidden');
                } else {
                    box.classList.add('hidden');
                }
            },

            updateAIProgress(pct, detail, key = '', current = 0, total = 0) {
                const bar = document.getElementById('ai-inline-bar-fill');
                const pctLabel = document.getElementById('ai-inline-percentage');
                const statusText = document.getElementById('ai-inline-status-text');
                const detailLabel = document.getElementById('ai-inline-detail');

                const clampedPct = Math.min(100, Math.max(0, Math.round(pct)));

                if (bar) bar.style.width = `${clampedPct}%`;
                if (pctLabel) pctLabel.textContent = `${clampedPct}%`;

                if (detailLabel) {
                    if (total > 0 && current > 0) {
                        const curMb = (current / (1024 * 1024)).toFixed(1);
                        const totMb = (total / (1024 * 1024)).toFixed(1);
                        detailLabel.textContent = `Mengunduh data: ${curMb} MB / ${totMb} MB`;
                    } else if (detail) {
                        detailLabel.textContent = detail;
                    }
                }

                if (statusText) {
                    if (clampedPct >= 100) {
                        statusText.textContent = 'Menyelesaikan pemotongan background...';
                    } else if (key && (key.includes('compute') || key.includes('inference'))) {
                        statusText.textContent = 'Menganalisis & memotong objek foto...';
                    } else if (key && (key.includes('model') || key.includes('weight') || key.includes('onnx') || key.includes('fetch'))) {
                        statusText.textContent = 'Mengunduh model AI ke browser...';
                    } else if (detail) {
                        statusText.textContent = detail;
                    } else {
                        statusText.textContent = 'Memproses hapus background...';
                    }
                }

                this.setAIModelStatusBadge('downloading', clampedPct);
            },

            async loadAIModule() {
                if (this.aiRemoveFn) return this.aiRemoveFn;

                if (typeof window.imglyRemoveBackground === 'function') {
                    this.aiRemoveFn = window.imglyRemoveBackground;
                    return this.aiRemoveFn;
                }
                if (window.imgly && typeof window.imgly.removeBackground === 'function') {
                    this.aiRemoveFn = window.imgly.removeBackground;
                    return this.aiRemoveFn;
                }
                if (typeof imglyRemoveBackground === 'function') {
                    this.aiRemoveFn = imglyRemoveBackground;
                    return this.aiRemoveFn;
                }

                if (!this.aiModulePromise) {
                    this.aiModulePromise = (async () => {
                        this.updateAIProgress(5, 'Menghubungkan ke repositori modul AI...', 'fetch:module');

                        try {
                            this.updateAIProgress(15, 'Mengunduh paket modul WebAssembly...', 'fetch:module');
                            const mod = await import('https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.7/+esm');
                            const fn = mod.default || mod.removeBackground || mod.imglyRemoveBackground || (typeof mod === 'function' ? mod : null);
                            if (typeof fn === 'function') {
                                this.aiRemoveFn = fn;
                                return fn;
                            }
                        } catch (err) {}

                        try {
                            this.updateAIProgress(25, 'Mengunduh paket dari cermin alternatif...', 'fetch:module');
                            const mod = await import('https://cdn.jsdelivr.net/npm/@imgly/background-removal/+esm');
                            const fn = mod.default || mod.removeBackground || mod.imglyRemoveBackground || (typeof mod === 'function' ? mod : null);
                            if (typeof fn === 'function') {
                                this.aiRemoveFn = fn;
                                return fn;
                            }
                        } catch (err) {}

                        try {
                            this.updateAIProgress(35, 'Mengunduh pustaka bundel AI...', 'fetch:module');
                            await new Promise((resolve, reject) => {
                                const script = document.createElement('script');
                                script.src = 'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.7/dist/bundle.js';
                                script.onload = () => resolve();
                                script.onerror = () => reject(new Error('Gagal memuat bundle skrip AI'));
                                document.head.appendChild(script);
                            });
                            const fn = window.imglyRemoveBackground || (window.imgly && window.imgly.removeBackground) || (typeof imglyRemoveBackground !== 'undefined' ? imglyRemoveBackground : null);
                            if (typeof fn === 'function') {
                                this.aiRemoveFn = fn;
                                return fn;
                            }
                        } catch (err) {}

                        throw new Error('Gagal mengunduh modul AI WebAssembly. Periksa koneksi internet Anda.');
                    })();
                }

                return await this.aiModulePromise;
            },

            async preloadAIModel() {
                if (this.isProcessingAI) return;
                this.isProcessingAI = true;
                this.showAIInlineProgress(true);
                this.updateAIProgress(0, 'Menyiapkan modul & model AI...', 'fetch:module');

                try {
                    const removeFn = await this.loadAIModule();
                    this.updateAIProgress(15, 'Mengunduh model AI ke cache browser...', 'fetch:model');

                    const dummyCanvas = document.createElement('canvas');
                    dummyCanvas.width = 64;
                    dummyCanvas.height = 64;
                    const dCtx = dummyCanvas.getContext('2d');
                    dCtx.fillStyle = '#ffffff';
                    dCtx.fillRect(0, 0, 64, 64);

                    await removeFn(dummyCanvas, {
                        progress: (key, current, total) => {
                            let pct = 0;
                            if (total > 0) {
                                pct = Math.min(99, Math.max(15, Math.round((current / total) * 100)));
                            } else {
                                pct = 50;
                            }
                            this.updateAIProgress(pct, 'Mengunduh model AI...', key, current, total);
                        },
                        model: 'medium'
                    });

                    this.updateAIProgress(100, 'Model AI berhasil diunduh & disimpan!', 'complete');
                    this.setAIModelStatusBadge('ready');
                    try {
                        localStorage.setItem('imgly_model_cached', 'true');
                    } catch (e) {}

                    setTimeout(() => {
                        this.showAIInlineProgress(false);
                        this.showToast('Model AI siap digunakan secara instan!', 'success');
                    }, 600);
                } catch (err) {
                    this.showAIInlineProgress(false);
                    this.setAIModelStatusBadge('not_downloaded');
                    this.showAlertDialog({
                        title: 'Gagal Mengunduh Model AI',
                        message: 'Gagal mengunduh model AI: ' + (err.message || err),
                        type: 'danger'
                    });
                } finally {
                    this.isProcessingAI = false;
                }
            },

            async removeBackgroundAI() {
                if (this.isProcessingAI) return;
                if (!this.processedImageSrc && !this.originalImageSrc) {
                    this.showToast('Silakan unggah foto terlebih dahulu.', 'warning');
                    return;
                }

                const btn = document.getElementById('btn-remove-bg');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                this.isProcessingAI = true;
                this.showAIInlineProgress(true);
                this.updateAIProgress(0, 'Menyiapkan modul & model AI...', 'fetch:module');

                try {
                    const removeFn = await this.loadAIModule();
                    if (typeof removeFn !== 'function') {
                        throw new Error('Modul AI gagal diinisialisasi.');
                    }

                    this.updateAIProgress(10, 'Mengunduh model AI ke browser...', 'fetch:model');

                    const source = this.processedImageSrc || this.originalImageSrc;
                    const blobResult = await removeFn(source, {
                        progress: (key, current, total) => {
                            let pct = 0;
                            if (total > 0) {
                                pct = Math.min(99, Math.max(10, Math.round((current / total) * 100)));
                            } else if (key && (key.includes('compute') || key.includes('inference'))) {
                                pct = 95;
                            } else {
                                pct = 45;
                            }

                            let detail = 'Mengunduh model AI...';
                            if (key && (key.includes('compute') || key.includes('inference'))) {
                                detail = 'Menganalisis dan memotong background foto...';
                            }
                            this.updateAIProgress(pct, detail, key, current, total);
                        },
                        model: 'medium',
                        output: {
                            format: 'image/png',
                            quality: 1.0,
                            type: 'blob'
                        }
                    });

                    this.updateAIProgress(98, 'Membersihkan sisa background...', 'compute:erode');

                    const rawImg = await new Promise((resolve, reject) => {
                        const img = new Image();
                        const u = URL.createObjectURL(blobResult);
                        img.onload = () => {
                            URL.revokeObjectURL(u);
                            resolve(img);
                        };
                        img.onerror = () => {
                            URL.revokeObjectURL(u);
                            reject(new Error('Gagal membaca gambar'));
                        };
                        img.src = u;
                    });

                    const cleanCanvas = document.createElement('canvas');
                    cleanCanvas.width = rawImg.naturalWidth || rawImg.width;
                    cleanCanvas.height = rawImg.naturalHeight || rawImg.height;
                    const cCtx = cleanCanvas.getContext('2d');
                    cCtx.drawImage(rawImg, 0, 0);

                    const imgData = cCtx.getImageData(0, 0, cleanCanvas.width, cleanCanvas.height);
                    const refinedData = this.erodeAlphaEdges(imgData, 0.9);
                    cCtx.putImageData(refinedData, 0, 0);

                    const refinedBlob = await new Promise((resolve) => {
                        cleanCanvas.toBlob((b) => resolve(b || blobResult), 'image/png');
                    });

                    this.updateAIProgress(100, 'Latar belakang berhasil dihapus!', 'complete');

                    const oldSrc = this.processedImageSrc;
                    const newUrl = this.createTrackedObjectUrl(refinedBlob);
                    if (oldSrc && oldSrc.startsWith('blob:')) {
                        this.revokeTrackedObjectUrl(oldSrc);
                    }

                    this.processedImageSrc = newUrl;
                    this.setAIModelStatusBadge('ready');
                    try {
                        localStorage.setItem('imgly_model_cached', 'true');
                    } catch (e) {}

                    this.initCropperIfNeeded();

                    setTimeout(() => {
                        this.showAIInlineProgress(false);
                        this.showToast('Latar belakang berhasil dihapus dengan AI!', 'success');
                    }, 600);

                } catch (err) {
                    this.showAIInlineProgress(false);
                    this.showAlertDialog({
                        title: 'Gagal Menghapus Background',
                        message: 'Terjadi kesalahan saat memproses foto dengan AI: ' + (err.message || err),
                        type: 'danger'
                    });
                } finally {
                    this.isProcessingAI = false;
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
            },

            erodeAlphaEdges(imageData, erosionPx = 0.9) {
                const width = imageData.width;
                const height = imageData.height;
                const src = imageData.data;
                const out = new Uint8ClampedArray(src);
                const alpha = new Uint8Array(width * height);

                for (let i = 0; i < src.length; i += 4) {
                    alpha[i >> 2] = src[i + 3];
                }

                const weight = Math.min(1.0, Math.max(0.0, erosionPx));
                const invWeight = 1.0 - weight;

                for (let y = 0; y < height; y++) {
                    const yOffset = y * width;
                    for (let x = 0; x < width; x++) {
                        const idx = yOffset + x;
                        const a = alpha[idx];
                        if (a === 0) continue;

                        const aUp = y > 0 ? alpha[idx - width] : 0;
                        const aDown = y < height - 1 ? alpha[idx + width] : 0;
                        const aLeft = x > 0 ? alpha[idx - 1] : 0;
                        const aRight = x < width - 1 ? alpha[idx + 1] : 0;

                        const aUL = (y > 0 && x > 0) ? alpha[idx - width - 1] : 0;
                        const aUR = (y > 0 && x < width - 1) ? alpha[idx - width + 1] : 0;
                        const aDL = (y < height - 1 && x > 0) ? alpha[idx + width - 1] : 0;
                        const aDR = (y < height - 1 && x < width - 1) ? alpha[idx + width + 1] : 0;

                        const minOrthogonal = Math.min(aUp, aDown, aLeft, aRight);
                        const minDiagonal = Math.min(aUL, aUR, aDL, aDR);
                        const minNeighbor = Math.min(minOrthogonal, minDiagonal);

                        const targetA = Math.round(a * invWeight + minNeighbor * weight);
                        const finalA = targetA < 20 ? 0 : targetA;

                        const pixelIdx = idx << 2;
                        out[pixelIdx + 3] = finalA;
                    }
                }

                return new ImageData(out, width, height);
            },

            restoreOriginalPhoto() {
                if (!this.originalImageSrc) return;
                const oldSrc = this.processedImageSrc;
                if (oldSrc && oldSrc.startsWith('blob:')) {
                    this.revokeTrackedObjectUrl(oldSrc);
                }
                this.processedImageSrc = this.originalImageSrc;
                this.initCropperIfNeeded();
                this.showToast('Foto dikembalikan ke versi asli.', 'info');
            },

            openManualEditModal() {
                if (!this.processedImageSrc && !this.originalImageSrc) {
                    this.showToast('Silakan unggah foto terlebih dahulu.', 'warning');
                    return;
                }

                const modal = document.getElementById('modal-manual-edit');
                const canvas = document.getElementById('manual-edit-canvas');
                if (!modal || !canvas) return;

                const currentSrc = this.processedImageSrc || this.originalImageSrc;
                const origSrc = this.originalImageSrc || this.processedImageSrc;

                const imgCurrent = new Image();
                const imgOrig = new Image();
                imgCurrent.crossOrigin = 'anonymous';
                imgOrig.crossOrigin = 'anonymous';

                let loadedCount = 0;
                const checkBothLoaded = () => {
                    loadedCount++;
                    if (loadedCount < 2) return;

                    canvas.width = imgCurrent.naturalWidth || imgCurrent.width;
                    canvas.height = imgCurrent.naturalHeight || imgCurrent.height;
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(imgCurrent, 0, 0);

                    const origCanvas = document.createElement('canvas');
                    origCanvas.width = canvas.width;
                    origCanvas.height = canvas.height;
                    const origCtx = origCanvas.getContext('2d');
                    origCtx.drawImage(imgOrig, 0, 0, canvas.width, canvas.height);
                    this.manualOriginalImageCanvas = origCanvas;

                    this.manualScratchCanvas = document.createElement('canvas');
                    this.manualScratchCtx = this.manualScratchCanvas.getContext('2d');

                    this.manualHistory = [ctx.getImageData(0, 0, canvas.width, canvas.height)];
                    this.manualHistoryIndex = 0;

                    const container = document.getElementById('manual-canvas-container');
                    const maxW = container ? Math.max(300, container.clientWidth - 48) : 800;
                    const maxH = container ? Math.max(300, container.clientHeight - 48) : 600;
                    const fitRatio = Math.min(1.0, maxW / canvas.width, maxH / canvas.height);

                    this.manualBaseWidth = Math.round(canvas.width * fitRatio);
                    this.manualBaseHeight = Math.round(canvas.height * fitRatio);
                    this.manualZoom = 1.0;
                    this.applyManualZoom();

                    this.setupManualCanvasEvents();
                    this.setManualEditTool('eraser');

                    modal.classList.remove('opacity-0', 'pointer-events-none');
                };

                imgCurrent.onload = checkBothLoaded;
                imgOrig.onload = checkBothLoaded;
                imgCurrent.onerror = () => this.showToast('Gagal memuat foto untuk diedit.', 'error');
                imgOrig.onerror = () => this.showToast('Gagal memuat foto asli.', 'error');

                imgCurrent.src = currentSrc;
                imgOrig.src = origSrc;
            },

            closeManualEditModal() {
                const modal = document.getElementById('modal-manual-edit');
                if (modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
                this.hideBrushCursor();
            },

            zoomManualEdit(delta) {
                this.manualZoom = Math.min(5.0, Math.max(0.25, Math.round((this.manualZoom + delta) * 100) / 100));
                this.applyManualZoom();
            },

            fitManualEditZoom() {
                this.manualZoom = 1.0;
                this.applyManualZoom();
            },

            applyManualZoom() {
                const canvas = document.getElementById('manual-edit-canvas');
                const zoomVal = document.getElementById('manual-zoom-val');
                if (zoomVal) zoomVal.textContent = Math.round(this.manualZoom * 100) + '%';
                if (canvas && this.manualBaseWidth && this.manualBaseHeight) {
                    canvas.style.width = Math.round(this.manualBaseWidth * this.manualZoom) + 'px';
                    canvas.style.height = Math.round(this.manualBaseHeight * this.manualZoom) + 'px';
                }
                this.updateBrushCursorSize();
            },

            updateBrushCursor(e) {
                const cursor = document.getElementById('manual-brush-cursor');
                const canvas = document.getElementById('manual-edit-canvas');
                if (!cursor || !canvas) return;

                const rect = canvas.getBoundingClientRect();
                const isInside = (
                    e.clientX >= rect.left &&
                    e.clientX <= rect.right &&
                    e.clientY >= rect.top &&
                    e.clientY <= rect.bottom
                );

                if (!isInside) {
                    cursor.classList.add('hidden');
                    return;
                }

                const visualScale = rect.width / canvas.width;
                const diameter = Math.max(4, this.manualBrushSize * visualScale);

                cursor.style.width = diameter + 'px';
                cursor.style.height = diameter + 'px';
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';

                if (this.manualEditTool === 'eraser') {
                    cursor.style.border = '2px solid rgba(255, 255, 255, 0.95)';
                    cursor.style.boxShadow = '0 0 0 1px rgba(0, 0, 0, 0.8), inset 0 0 0 1px rgba(0, 0, 0, 0.8)';
                    cursor.style.backgroundColor = 'rgba(239, 68, 68, 0.18)';
                } else {
                    cursor.style.border = '2px solid rgba(255, 255, 255, 0.95)';
                    cursor.style.boxShadow = '0 0 0 1px rgba(0, 0, 0, 0.8), inset 0 0 0 1px rgba(0, 0, 0, 0.8)';
                    cursor.style.backgroundColor = 'rgba(59, 130, 246, 0.18)';
                }

                cursor.classList.remove('hidden');
            },

            updateBrushCursorSize() {
                const cursor = document.getElementById('manual-brush-cursor');
                const canvas = document.getElementById('manual-edit-canvas');
                if (!cursor || !canvas) return;

                const rect = canvas.getBoundingClientRect();
                const visualScale = rect.width / canvas.width;
                const diameter = Math.max(4, this.manualBrushSize * visualScale);

                cursor.style.width = diameter + 'px';
                cursor.style.height = diameter + 'px';
            },

            hideBrushCursor() {
                const cursor = document.getElementById('manual-brush-cursor');
                if (cursor) cursor.classList.add('hidden');
            },

            setManualEditTool(tool) {
                this.manualEditTool = tool;
                const btnEraser = document.getElementById('tool-btn-eraser');
                const btnRestore = document.getElementById('tool-btn-restore');
                if (!btnEraser || !btnRestore) return;

                if (tool === 'eraser') {
                    btnEraser.className = 'px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-blue-600 text-white shadow-sm';
                    btnRestore.className = 'px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700';
                } else {
                    btnRestore.className = 'px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-blue-600 text-white shadow-sm';
                    btnEraser.className = 'px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700';
                }
            },

            handleManualBrushSizeChange(val) {
                this.manualBrushSize = parseInt(val, 10) || 20;
                const label = document.getElementById('manual-brush-size-val');
                if (label) label.textContent = `${this.manualBrushSize}px`;
                this.updateBrushCursorSize();
            },

            setupManualCanvasEvents() {
                const canvas = document.getElementById('manual-edit-canvas');
                const container = document.getElementById('manual-canvas-container');
                if (!canvas || canvas.dataset.eventsAttached === 'true') return;
                canvas.dataset.eventsAttached = 'true';

                const getCanvasPos = (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;
                    const clientX = e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches && e.touches.length > 0 ? e.touches[0].clientY : e.clientY;
                    return {
                        x: (clientX - rect.left) * scaleX,
                        y: (clientY - rect.top) * scaleY
                    };
                };

                const startDraw = (e) => {
                    e.preventDefault();
                    this.manualIsDrawing = true;
                    const pos = getCanvasPos(e);
                    this.manualLastX = pos.x;
                    this.manualLastY = pos.y;
                    this.applyBrushStroke(pos.x, pos.y, pos.x, pos.y);
                };

                const moveDraw = (e) => {
                    if (this.manualIsDrawing) {
                        e.preventDefault();
                        const pos = getCanvasPos(e);
                        this.applyBrushStroke(this.manualLastX, this.manualLastY, pos.x, pos.y);
                        this.manualLastX = pos.x;
                        this.manualLastY = pos.y;
                    }
                    this.updateBrushCursor(e);
                };

                const endDraw = (e) => {
                    if (!this.manualIsDrawing) return;
                    this.manualIsDrawing = false;
                    this.recordManualHistory();
                };

                canvas.addEventListener('mousedown', startDraw);
                window.addEventListener('mousemove', moveDraw);
                window.addEventListener('mouseup', endDraw);

                canvas.addEventListener('touchstart', startDraw, { passive: false });
                window.addEventListener('touchmove', moveDraw, { passive: false });
                window.addEventListener('touchend', endDraw);

                canvas.addEventListener('mouseleave', () => this.hideBrushCursor());
                if (container) {
                    container.addEventListener('wheel', (e) => {
                        e.preventDefault();
                        const delta = e.deltaY < 0 ? 0.2 : -0.2;
                        this.zoomManualEdit(delta);
                    }, { passive: false });
                }
            },

            applyBrushStroke(x0, y0, x1, y1) {
                const canvas = document.getElementById('manual-edit-canvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const radius = this.manualBrushSize / 2;

                if (this.manualEditTool === 'eraser') {
                    ctx.save();
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.lineWidth = this.manualBrushSize;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    ctx.strokeStyle = 'rgba(0,0,0,1)';
                    ctx.beginPath();
                    ctx.moveTo(x0, y0);
                    ctx.lineTo(x1, y1);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.arc(x1, y1, radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(0,0,0,1)';
                    ctx.fill();
                    ctx.restore();
                } else if (this.manualEditTool === 'restore' && this.manualOriginalImageCanvas) {
                    const minX = Math.max(0, Math.floor(Math.min(x0, x1) - radius - 2));
                    const minY = Math.max(0, Math.floor(Math.min(y0, y1) - radius - 2));
                    const maxX = Math.min(canvas.width, Math.ceil(Math.max(x0, x1) + radius + 2));
                    const maxY = Math.min(canvas.height, Math.ceil(Math.max(y0, y1) + radius + 2));
                    const bw = maxX - minX;
                    const bh = maxY - minY;

                    if (bw <= 0 || bh <= 0) return;

                    if (!this.manualScratchCanvas) {
                        this.manualScratchCanvas = document.createElement('canvas');
                        this.manualScratchCtx = this.manualScratchCanvas.getContext('2d');
                    }

                    this.manualScratchCanvas.width = bw;
                    this.manualScratchCanvas.height = bh;
                    const sCtx = this.manualScratchCtx;
                    sCtx.clearRect(0, 0, bw, bh);

                    sCtx.lineWidth = this.manualBrushSize;
                    sCtx.lineCap = 'round';
                    sCtx.lineJoin = 'round';
                    sCtx.strokeStyle = 'rgba(0,0,0,1)';
                    sCtx.beginPath();
                    sCtx.moveTo(x0 - minX, y0 - minY);
                    sCtx.lineTo(x1 - minX, y1 - minY);
                    sCtx.stroke();
                    sCtx.beginPath();
                    sCtx.arc(x1 - minX, y1 - minY, radius, 0, Math.PI * 2);
                    sCtx.fillStyle = 'rgba(0,0,0,1)';
                    sCtx.fill();

                    sCtx.globalCompositeOperation = 'source-in';
                    sCtx.drawImage(this.manualOriginalImageCanvas, minX, minY, bw, bh, 0, 0, bw, bh);

                    ctx.save();
                    ctx.globalCompositeOperation = 'source-over';
                    ctx.drawImage(this.manualScratchCanvas, minX, minY);
                    ctx.restore();
                }
            },

            recordManualHistory() {
                const canvas = document.getElementById('manual-edit-canvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (this.manualHistoryIndex < this.manualHistory.length - 1) {
                    this.manualHistory = this.manualHistory.slice(0, this.manualHistoryIndex + 1);
                }
                this.manualHistory.push(ctx.getImageData(0, 0, canvas.width, canvas.height));
                if (this.manualHistory.length > 20) {
                    this.manualHistory.shift();
                }
                this.manualHistoryIndex = this.manualHistory.length - 1;
            },

            undoManualEdit() {
                if (this.manualHistoryIndex > 0) {
                    this.manualHistoryIndex--;
                    const canvas = document.getElementById('manual-edit-canvas');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.putImageData(this.manualHistory[this.manualHistoryIndex], 0, 0);
                    }
                }
            },

            redoManualEdit() {
                if (this.manualHistoryIndex < this.manualHistory.length - 1) {
                    this.manualHistoryIndex++;
                    const canvas = document.getElementById('manual-edit-canvas');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.putImageData(this.manualHistory[this.manualHistoryIndex], 0, 0);
                    }
                }
            },

            saveManualEdit() {
                const canvas = document.getElementById('manual-edit-canvas');
                if (!canvas) return;

                canvas.toBlob((blob) => {
                    if (!blob) {
                        this.showToast('Gagal menyimpan hasil edit.', 'error');
                        return;
                    }
                    const oldSrc = this.processedImageSrc;
                    const newUrl = this.createTrackedObjectUrl(blob);
                    if (oldSrc && oldSrc.startsWith('blob:')) {
                        this.revokeTrackedObjectUrl(oldSrc);
                    }
                    this.processedImageSrc = newUrl;
                    this.initCropperIfNeeded();
                    this.closeManualEditModal();
                    this.showToast('Hasil edit manual berhasil disimpan!', 'success');
                }, 'image/png');
            },

            showConfirmDialog(options = {}) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('modal-custom-dialog');
                    const box = document.getElementById('custom-dialog-box');
                    const titleEl = document.getElementById('dialog-title');
                    const msgEl = document.getElementById('dialog-message');
                    const iconEl = document.getElementById('dialog-icon');
                    const iconContainer = document.getElementById('dialog-icon-container');
                    const promptContainer = document.getElementById('dialog-prompt-container');
                    const btnCancel = document.getElementById('dialog-btn-cancel');
                    const btnConfirm = document.getElementById('dialog-btn-confirm');

                    if (!modal || !box) {
                        resolve(false);
                        return;
                    }

                    titleEl.textContent = options.title || 'Konfirmasi';
                    msgEl.textContent = options.message || '';
                    promptContainer.classList.add('hidden');
                    btnCancel.classList.remove('hidden');
                    btnCancel.textContent = options.cancelText || 'Batal';
                    btnConfirm.textContent = options.confirmText || 'Konfirmasi';

                    const type = options.type || 'info';
                    if (type === 'danger') {
                        iconContainer.className = 'size-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'warning';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-all';
                    } else if (type === 'warning') {
                        iconContainer.className = 'size-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'warning';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition-all';
                    } else {
                        iconContainer.className = 'size-12 rounded-2xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'help';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all';
                    }

                    const cleanup = (result) => {
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        box.classList.remove('scale-100');
                        box.classList.add('scale-95');
                        btnConfirm.onclick = null;
                        btnCancel.onclick = null;
                        resolve(result);
                    };

                    btnConfirm.onclick = () => cleanup(true);
                    btnCancel.onclick = () => cleanup(false);

                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                });
            },

            showAlertDialog(options = {}) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('modal-custom-dialog');
                    const box = document.getElementById('custom-dialog-box');
                    const titleEl = document.getElementById('dialog-title');
                    const msgEl = document.getElementById('dialog-message');
                    const iconEl = document.getElementById('dialog-icon');
                    const iconContainer = document.getElementById('dialog-icon-container');
                    const promptContainer = document.getElementById('dialog-prompt-container');
                    const btnCancel = document.getElementById('dialog-btn-cancel');
                    const btnConfirm = document.getElementById('dialog-btn-confirm');

                    if (!modal || !box) {
                        resolve();
                        return;
                    }

                    titleEl.textContent = options.title || 'Pemberitahuan';
                    msgEl.textContent = typeof options === 'string' ? options : (options.message || '');
                    promptContainer.classList.add('hidden');
                    btnCancel.classList.add('hidden');
                    btnConfirm.textContent = options.buttonText || 'Mengerti';

                    const type = options.type || 'info';
                    if (type === 'danger' || type === 'error') {
                        iconContainer.className = 'size-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'error';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all';
                    } else if (type === 'warning') {
                        iconContainer.className = 'size-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'warning';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all';
                    } else {
                        iconContainer.className = 'size-12 rounded-2xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0';
                        iconEl.textContent = 'info';
                        btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all';
                    }

                    const cleanup = () => {
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        box.classList.remove('scale-100');
                        box.classList.add('scale-95');
                        btnConfirm.onclick = null;
                        btnCancel.onclick = null;
                        resolve();
                    };

                    btnConfirm.onclick = cleanup;

                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                });
            },

            showPromptDialog(options = {}) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('modal-custom-dialog');
                    const box = document.getElementById('custom-dialog-box');
                    const titleEl = document.getElementById('dialog-title');
                    const msgEl = document.getElementById('dialog-message');
                    const iconEl = document.getElementById('dialog-icon');
                    const iconContainer = document.getElementById('dialog-icon-container');
                    const promptContainer = document.getElementById('dialog-prompt-container');
                    const promptInput = document.getElementById('dialog-prompt-input');
                    const btnCancel = document.getElementById('dialog-btn-cancel');
                    const btnConfirm = document.getElementById('dialog-btn-confirm');

                    if (!modal || !box) {
                        resolve(null);
                        return;
                    }

                    titleEl.textContent = options.title || 'Input';
                    msgEl.textContent = options.message || '';
                    promptContainer.classList.remove('hidden');
                    promptInput.value = options.defaultValue || '';
                    promptInput.placeholder = options.placeholder || '';
                    btnCancel.classList.remove('hidden');
                    btnCancel.textContent = options.cancelText || 'Batal';
                    btnConfirm.textContent = options.confirmText || 'Kirim';

                    iconContainer.className = 'size-12 rounded-2xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0';
                    iconEl.textContent = 'edit';
                    btnConfirm.className = 'px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-semibold shadow-sm transition-all';

                    const cleanup = (result) => {
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        box.classList.remove('scale-100');
                        box.classList.add('scale-95');
                        btnConfirm.onclick = null;
                        btnCancel.onclick = null;
                        promptInput.onkeydown = null;
                        resolve(result);
                    };

                    btnConfirm.onclick = () => cleanup(promptInput.value);
                    btnCancel.onclick = () => cleanup(null);
                    promptInput.onkeydown = (e) => {
                        if (e.key === 'Enter') cleanup(promptInput.value);
                        if (e.key === 'Escape') cleanup(null);
                    };

                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                    setTimeout(() => promptInput.focus(), 100);
                });
            },

            async resetProject() {
                const confirmed = await this.showConfirmDialog({
                    title: 'Mulai Ulang Proyek?',
                    message: 'Apakah Anda yakin ingin memulai ulang? Semua pengaturan dan foto di kanvas akan dikembalikan ke awal.',
                    confirmText: 'Ya, Mulai Ulang',
                    cancelText: 'Batal',
                    type: 'danger'
                });
                if (!confirmed) return;

                this.revokeAllObjectUrls();
                this.originalImageSrc = null;
                this.processedImageSrc = null;
                this.croppedImageSrc = null;
                this.activeBgColor = 'transparent';
                this.photosOnCanvas = [];
                this.selectedPhotoIds.clear();
                this.history = [];
                this.historyIndex = -1;

                if (this.cropperInstance) {
                    this.cropperInstance.destroy();
                    this.cropperInstance = null;
                }

                const input = document.getElementById('photo-upload');
                if (input) input.value = '';

                this.sizesConfig.forEach(s => {
                    this.selectedQuantities[s.id] = 0;
                });
                this.confirmedSizes.clear();
                this.renderSizeCards();
                this.updateTotalQuantityDisplay();

                this.resetEnhancements(false);
                this.goToStep(1);
                this.showToast('Proyek berhasil direset bersih.', 'info');
            },

            toggleShortcutModal(show) {
                const modal = document.getElementById('modal-shortcut');
                if (!modal) return;
                if (show) {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
            },

            showToast(message, type = 'info') {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                const id = `toast-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`;
                toast.id = id;

                let icon = 'info';
                let borderClass = 'border-slate-200 dark:border-slate-700';
                let iconColor = 'text-slate-800 dark:text-slate-200';

                if (type === 'success') {
                    icon = 'check_circle';
                    iconColor = 'text-emerald-600 dark:text-emerald-400';
                    borderClass = 'border-emerald-200 dark:border-emerald-900';
                } else if (type === 'warning') {
                    icon = 'warning';
                    iconColor = 'text-amber-600 dark:text-amber-400';
                    borderClass = 'border-amber-200 dark:border-amber-900';
                } else if (type === 'error') {
                    icon = 'error';
                    iconColor = 'text-rose-600 dark:text-rose-400';
                    borderClass = 'border-rose-200 dark:border-rose-900';
                }

                toast.className = `pointer-events-auto bg-white dark:bg-slate-900 border ${borderClass} rounded-2xl p-3.5 shadow-floating flex items-center gap-3 transform translate-y-4 opacity-0 transition-all duration-300`;

                const iconSpan = document.createElement('span');
                iconSpan.className = `material-symbols-outlined ${iconColor} text-xl shrink-0`;
                iconSpan.textContent = icon;

                const msgP = document.createElement('p');
                msgP.className = 'text-xs font-semibold text-slate-800 dark:text-slate-200 flex-1 leading-snug';
                msgP.textContent = String(message);

                const closeBtn = document.createElement('button');
                closeBtn.className = 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1';
                closeBtn.onclick = () => toast.remove();
                const closeIcon = document.createElement('span');
                closeIcon.className = 'material-symbols-outlined text-[16px]';
                closeIcon.textContent = 'close';
                closeBtn.appendChild(closeIcon);

                toast.appendChild(iconSpan);
                toast.appendChild(msgP);
                toast.appendChild(closeBtn);

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.remove('translate-y-4', 'opacity-0');
                });

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            },

            openFeedbackModal() {
                const modal = document.getElementById('modal-feedback');
                const box = document.getElementById('feedback-modal-box');
                const textarea = document.getElementById('feedback-message');
                if (!modal || !box) return;

                modal.classList.remove('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');

                this.feedbackKeydownHandler = (e) => {
                    if (e.key === 'Escape') this.closeFeedbackModal();
                };
                window.addEventListener('keydown', this.feedbackKeydownHandler);

                if (textarea) {
                    textarea.value = '';
                    const counter = document.getElementById('feedback-char-count');
                    if (counter) counter.textContent = '0 / 500';
                    textarea.oninput = () => {
                        if (counter) counter.textContent = `${textarea.value.length} / 500`;
                    };
                    setTimeout(() => textarea.focus(), 150);
                }
            },

            closeFeedbackModal() {
                const modal = document.getElementById('modal-feedback');
                const box = document.getElementById('feedback-modal-box');
                if (!modal || !box) return;

                modal.classList.add('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');

                const namaEl = document.getElementById('feedback-nama');
                const kontakEl = document.getElementById('feedback-kontak');
                const textarea = document.getElementById('feedback-message');
                if (namaEl) namaEl.value = '';
                if (kontakEl) kontakEl.value = '';
                if (textarea) textarea.value = '';

                if (this.feedbackKeydownHandler) {
                    window.removeEventListener('keydown', this.feedbackKeydownHandler);
                    this.feedbackKeydownHandler = null;
                }
            },

            selectFeedbackCategory(category, btnEl) {
                this.activeFeedbackCategory = category;
                const buttons = document.querySelectorAll('#feedback-category-group .feedback-cat-btn');
                buttons.forEach(btn => {
                    btn.className = 'feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800';
                });
                if (btnEl) {
                    btnEl.className = 'feedback-cat-btn py-1.5 px-2 rounded-xl border text-[11px] font-semibold transition-all text-center bg-slate-900 text-white dark:bg-white dark:text-slate-900 border-slate-900 dark:border-white shadow-xs';
                }
            },

            async submitFeedback() {
                const textarea = document.getElementById('feedback-message');
                const btnSubmit = document.getElementById('btn-submit-feedback');
                const namaEl = document.getElementById('feedback-nama');
                const kontakEl = document.getElementById('feedback-kontak');
                if (!textarea) return;

                const text = textarea.value.trim();
                if (!text) {
                    this.showToast('Silakan tuliskan saran atau masukan Anda.', 'warning');
                    textarea.focus();
                    return;
                }

                let origBtnContent = '';
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    origBtnContent = btnSubmit.innerHTML;
                    btnSubmit.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] animate-spin">autorenew</span>
                        <span>Mengirim...</span>
                    `;
                }

                try {
                    const response = await fetch('../tracker/feedback_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            nama: namaEl ? namaEl.value.trim() : '',
                            kontak: kontakEl ? kontakEl.value.trim() : '',
                            kategori: this.activeFeedbackCategory || 'Saran Fitur',
                            isi_feedback: text,
                            user_agent: navigator.userAgent || 'Unknown Browser'
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        this.showToast(result.message || 'Terima kasih! Saran & masukan Anda berhasil dikirim ke server.', 'success');
                        this.closeFeedbackModal();
                    } else {
                        throw new Error(result.message || 'Terjadi kendala saat menyimpan masukan ke server.');
                    }
                } catch (error) {
                    console.error('Feedback submission error:', error);
                    this.showToast(`Gagal mengirim masukan: ${error.message || 'Periksa koneksi Anda.'}`, 'error');
                } finally {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = origBtnContent;
                    }
                }
            }
        };

        window.alert = (msg) => { App.showAlertDialog({ message: String(msg) }); };
        window.confirm = (msg) => { return App.showConfirmDialog({ message: String(msg) }); };
        window.prompt = (msg, def) => { return App.showPromptDialog({ message: String(msg), defaultValue: def }); };

        App.init();

        window.evalConfig = <?= json_encode($evalConfig, JSON_UNESCAPED_UNICODE) ?>;

const genZEvalAnswers = {};
let genZEvalKeyHandler = null;

function escapeEvalHTML(str) {
    if (str === null || str === undefined) return "";
    return String(str).replace(/[&<>'"]/g, function(tag) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag;
    });
}

function renderGenZQuestions() {
    const container = document.getElementById('genz-questions-container');
    if (!container || !window.evalConfig || !Array.isArray(window.evalConfig.questions)) return;

    const questions = window.evalConfig.questions;
    const colors = [
        'from-amber-300 to-yellow-400',
        'from-pink-400 to-fuchsia-500',
        'from-purple-400 to-violet-500',
        'from-emerald-300 to-teal-400',
        'from-sky-300 to-cyan-400',
        'from-orange-300 to-red-400',
        'from-teal-300 to-emerald-400'
    ];

    let html = '';
    questions.forEach((q, idx) => {
        const qId = q.id || ('q_' + (idx + 1));
        const num = q.num || (idx + 1);
        const color = q.color || colors[idx % colors.length];
        const title = q.title || ('Pertanyaan ' + num);
        const options = Array.isArray(q.options) && q.options.length > 0 ? q.options : ['Ya', 'Tidak'];
        const selectedVal = genZEvalAnswers[qId] || '';

        html += `
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-3.5 transition-all duration-300 hover:border-white/20">
                <div class="flex items-center justify-between mb-2 gap-2">
                    <label class="text-xs sm:text-sm font-bold text-slate-100 flex items-center gap-2">
                        <span class="size-6 rounded-full bg-gradient-to-br ${color} text-slate-950 flex items-center justify-center text-xs font-black shadow-lg shrink-0">${num}</span>
                        <span>${escapeEvalHTML(title)}</span>
                    </label>
                    <span class="q-status-badge text-[10px] font-bold ${selectedVal ? 'text-emerald-200 bg-emerald-400/10 border-emerald-400/30' : 'text-amber-200 bg-amber-400/10 border-amber-400/30'} px-2 py-0.5 rounded-full border backdrop-blur-sm shrink-0" data-q="${qId}">
                        ${selectedVal ? '✅ Terjawab' : 'Wajib diisi'}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-${Math.min(options.length, 3)} gap-2" data-question="${qId}">
        `;

        options.forEach(opt => {
            const isSelected = selectedVal === opt;
            const optEscaped = escapeEvalHTML(opt);
            const optSafeParam = opt.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

            const btnClasses = isSelected
                ? 'border-fuchsia-400/50 bg-gradient-to-r from-fuchsia-500/25 to-cyan-400/25 shadow-lg shadow-fuchsia-500/20 text-white font-bold scale-[1.02]'
                : 'border-white/10 bg-white/5 text-slate-200 font-medium hover:bg-white/10 hover:border-white/20';

            html += `
                <button type="button" onclick="selectGenZChip(this, '${qId}', '${optSafeParam}')" class="genz-chip py-2.5 px-3 rounded-xl border backdrop-blur-md ${btnClasses} text-xs text-left transition-all duration-200 active:scale-95 flex items-center gap-1.5 cursor-pointer">
                    <span>${optEscaped}</span>
                </button>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    checkGenZEvalProgress();
}

function showGenZEvalModal() {
    if (!window.evalConfig || window.evalConfig.enabled === false) {
        const modal = document.getElementById('modal-genz-eval');
        if (modal) modal.style.display = 'none';
        return;
    }

    const modal = document.getElementById('modal-genz-eval');
    const box = document.getElementById('genz-modal-box');
    if (!modal || !box) return;

    renderGenZQuestions();

    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100');
    box.classList.remove('scale-95');
    box.classList.add('scale-100');

    if (!genZEvalKeyHandler) {
        genZEvalKeyHandler = (e) => {
            if (e.key === 'Escape') closeGenZEvalModal();
        };
        window.addEventListener('keydown', genZEvalKeyHandler);
    }
}

function closeGenZEvalModal() {
    const modal = document.getElementById('modal-genz-eval');
    const box = document.getElementById('genz-modal-box');
    if (box) {
        box.classList.remove('scale-100');
        box.classList.add('scale-95');
    }
    if (modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    if (genZEvalKeyHandler) {
        window.removeEventListener('keydown', genZEvalKeyHandler);
        genZEvalKeyHandler = null;
    }

    setTimeout(() => {
        const successOverlay = document.getElementById('genz-success-overlay');
        if (successOverlay) {
            successOverlay.classList.add('opacity-0', 'pointer-events-none');
            successOverlay.classList.remove('opacity-100', 'pointer-events-auto');
        }
    }, 300);
}

function selectGenZChip(btn, questionKey, answerValue) {
    genZEvalAnswers[questionKey] = answerValue;

    const parent = btn.closest('[data-question]');
    if (parent) {
        parent.querySelectorAll('.genz-chip').forEach(b => {
            b.classList.remove(
                'border-fuchsia-400/50', 'bg-gradient-to-r', 'from-fuchsia-500/25', 'to-cyan-400/25',
                'shadow-lg', 'shadow-fuchsia-500/20', 'text-white', 'font-bold', 'scale-[1.02]'
            );
            b.classList.add('border-white/10', 'bg-white/5', 'text-slate-200', 'font-medium');
        });
        btn.classList.remove('border-white/10', 'bg-white/5', 'text-slate-200', 'font-medium');
        btn.classList.add(
            'border-fuchsia-400/50', 'bg-gradient-to-r', 'from-fuchsia-500/25', 'to-cyan-400/25',
            'shadow-lg', 'shadow-fuchsia-500/20', 'text-white', 'font-bold', 'scale-[1.02]'
        );
    }

    const badge = document.querySelector(`.q-status-badge[data-q="${questionKey}"]`);
    if (badge) {
        badge.textContent = '✅ Terjawab';
        badge.className = 'q-status-badge text-[10px] font-bold text-emerald-200 bg-emerald-400/10 px-2 py-0.5 rounded-full border border-emerald-400/30 backdrop-blur-sm';
    }

    checkGenZEvalProgress();
}

function checkGenZEvalProgress() {
    const questions = (window.evalConfig && Array.isArray(window.evalConfig.questions)) ? window.evalConfig.questions : [];
    const total = questions.length;
    const answered = questions.filter(q => !!genZEvalAnswers[q.id || ('q_' + q.num)]).length;

    const progressEl = document.getElementById('genz-eval-progress');
    if (progressEl) {
        progressEl.textContent = `${answered} / ${total} terjawab`;
        if (answered >= total && total > 0) {
            progressEl.className = 'text-xs font-bold text-emerald-200 bg-emerald-400/10 px-3 py-1.5 rounded-full border border-emerald-400/30 backdrop-blur-sm';
        } else {
            progressEl.className = 'text-xs font-bold text-slate-200 bg-white/10 px-3 py-1.5 rounded-full border border-white/10 backdrop-blur-sm';
        }
    }

    const submitBtn = document.getElementById('btn-submit-genz-eval');
    if (submitBtn) {
        if (answered >= total && total > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-40', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.add('cursor-pointer');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-40', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.remove('cursor-pointer');
        }
    }
}

async function submitGenZEval() {
    const questions = (window.evalConfig && Array.isArray(window.evalConfig.questions)) ? window.evalConfig.questions : [];
    const unanswered = questions.filter(q => !genZEvalAnswers[q.id || ('q_' + q.num)]);
    if (unanswered.length > 0) {
        if (typeof App !== 'undefined' && App.showToast) {
            App.showToast(`Jawab dulu semua ${questions.length} pertanyaan ya kak! 🙏`, 'warning');
        }
        return;
    }

    const submitBtn = document.getElementById('btn-submit-genz-eval');
    const origContent = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="material-symbols-outlined text-[16px] animate-spin">autorenew</span>
            <span>Mengirim... 🛸</span>
        `;
    }

    try {
        const answersArray = questions.map((q, idx) => {
            const qId = q.id || ('q_' + (idx + 1));
            return {
                id: qId,
                title: q.title || `Pertanyaan ${idx + 1}`,
                answer: genZEvalAnswers[qId] || ''
            };
        });

        const payload = {
            type: 'post_print_eval',
            eval_speed: genZEvalAnswers['speed'] || (answersArray[0] ? answersArray[0].answer : ''),
            eval_crop: genZEvalAnswers['crop'] || (answersArray[1] ? answersArray[1].answer : ''),
            eval_layout: genZEvalAnswers['layout'] || (answersArray[2] ? answersArray[2].answer : ''),
            eval_cut: genZEvalAnswers['cut'] || (answersArray[3] ? answersArray[3].answer : ''),
            eval_perf: genZEvalAnswers['perf'] || (answersArray[4] ? answersArray[4].answer : ''),
            answers: answersArray,
            user_agent: navigator.userAgent || 'Unknown'
        };

        const res = await fetch('../tracker/feedback_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok && data.success) {
            const successOverlay = document.getElementById('genz-success-overlay');
            if (successOverlay) {
                successOverlay.classList.remove('opacity-0', 'pointer-events-none');
                successOverlay.classList.add('opacity-100', 'pointer-events-auto');
            }

            try {
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 70, spread: 80, origin: { y: 0.6 } });
                }
            } catch(e) {}

            setTimeout(() => {
                closeGenZEvalModal();
            }, 3000);
        } else {
            throw new Error(data.message || 'Gagal mengirim evaluasi.');
        }
    } catch (err) {
        console.error('Error submitGenZEval:', err);
        if (typeof App !== 'undefined' && App.showToast) {
            App.showToast('Gagal mengirim review: ' + (err.message || 'Periksa koneksi.'), 'error');
        } else {
            alert('Gagal mengirim review: ' + (err.message || 'Periksa koneksi.'));
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origContent;
        }
    }
}

        window.showGenZEvalModal = showGenZEvalModal;
        window.closeGenZEvalModal = closeGenZEvalModal;
        window.selectGenZChip = selectGenZChip;
        window.submitGenZEval = submitGenZEval;

        (async function syncEvaluasiConfig() {
            const apiEndpoints = [
                'https://turikaleprint.space/tracker/api/evaluasi.php?action=config',
                '../tracker/api/evaluasi.php?action=config',
                '../tracker/evaluasi_config.json'
            ];

            for (const url of apiEndpoints) {
                try {
                    const res = await fetch(url, { cache: 'no-store' });
                    if (res.ok) {
                        const json = await res.json();
                        const cfg = json.data || json;
                        if (cfg && typeof cfg.enabled !== 'undefined') {
                            window.evalConfig = cfg;
                            const modal = document.getElementById('modal-genz-eval');
                            if (modal && cfg.enabled === false) {
                                modal.style.display = 'none';
                            }
                            break;
                        }
                    }
                } catch(e) {}
            }
        })();
    </script>
    <script>
        (function(){
            try {
                var d = {
                    page_url: window.location.href,
                    referrer: document.referrer || '',
                    screen_width: window.screen ? window.screen.width : 0
                };
                if (navigator.sendBeacon) {
                    var blob = new Blob([JSON.stringify(d)], {type: 'application/json'});
                    navigator.sendBeacon('/api/track.php', blob);
                } else {
                    fetch('/api/track.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(d),
                        keepalive: true
                    }).catch(function(){});
                }
            } catch(e){}
        })();
    </script>
</body>
</html>
