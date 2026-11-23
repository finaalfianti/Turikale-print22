const sizes = [
    { label: "2x3", desc: "21.6x27.9 mm", widthMm: 21.6, heightMm: 27.9 },
    { label: "3x4", desc: "27.9x38.1 mm", widthMm: 27.9, heightMm: 38.1 },
    { label: "4x6", desc: "38.1x55.9 mm", widthMm: 38.1, heightMm: 55.9 },
    { label: "2R", desc: "6x9 cm", widthMm: 60, heightMm: 90 },
    { label: "3R", desc: "8.9x12.7 cm", widthMm: 89, heightMm: 127 },
    { label: "4R", desc: "10.2x15.2 cm", widthMm: 102, heightMm: 152 },
    { label: "5R", desc: "12.7x17.8 cm", widthMm: 127, heightMm: 178 }
];

let currentCropper = null;
let selectedSize = sizes[1];
let selectedBgColor = 'transparent';
let currentRotation = 0;
let currentStep = 1;
let activeSizeIndex = 1;
let confirmedCrops = {};

let originalImageFile = null;
let originalImageURL = null;
let bgRemovedImageURL = null;

let currentFilter = { b: 100, c: 100, custom: '' };
let lastCropCount = 0;
let hasSavedPrintLayout = false;
let currentZoom = 1.0;

function goToStep(step) {
    if (step > 1 && !currentCropper) {
        showCustomAlert('Please upload a photo first to continue.');
        return;
    }

    document.querySelectorAll('.nav-step').forEach(el => {
        const s = parseInt(el.dataset.step);
        if (s === step) {
            el.className = "nav-step active font-['Inter'] text-sm tracking-[0.1em] uppercase font-medium text-[#1A1C1E] dark:text-white border-b-2 border-[#1A1C1E] dark:border-zinc-100 pb-1 scale-100 active:scale-98 transition-transform cursor-pointer flex items-center h-full pt-1";
        } else {
            el.className = "nav-step font-['Inter'] text-sm tracking-[0.1em] uppercase font-medium text-zinc-400 dark:text-zinc-600 hover:text-[#1A1C1E] dark:hover:text-zinc-200 transition-colors duration-300 scale-100 active:scale-98 transition-transform cursor-pointer flex items-center h-full";
        }
    });

    document.getElementById('step-upload').classList.add('hidden');
    document.getElementById('step-size').classList.add('hidden');
    document.getElementById('step-print').classList.add('hidden');

    if (step === 1) document.getElementById('step-upload').classList.remove('hidden');

    if (step === 2) {
        document.getElementById('step-size').classList.remove('hidden');
        document.getElementById('step-size').classList.add('flex');
        if (currentCropper) {
            setTimeout(() => {
                if (currentCropper && currentCropper.container) {
                    currentCropper.resize();
                }
            }, 150);
            updateConfirmButton();
        }
    }

    if (step === 3) {
        const totalCrops = Object.values(confirmedCrops).reduce((sum, arr) => sum + arr.length, 0);
        if (totalCrops === 0) {
            showCustomAlert('Silakan konfirmasi setidaknya satu crop terlebih dahulu.');
            document.getElementById('step-print').classList.add('hidden');
            document.getElementById('step-size').classList.remove('hidden');
            document.getElementById('step-size').classList.add('flex');
            currentStep = 2;
            return;
        }
        document.getElementById('step-print').classList.remove('hidden');
        document.getElementById('step-print').classList.add('flex');

        autoFillPaper();
    }
    currentStep = step;
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (originalImageURL) URL.revokeObjectURL(originalImageURL);
    if (bgRemovedImageURL) URL.revokeObjectURL(bgRemovedImageURL);

    document.getElementById('bg-remove-toggle').checked = false;
    bgRemovedImageURL = null;
    document.getElementById('bg-remove-loading').classList.add('hidden');
    document.getElementById('bg-remove-loading').classList.remove('flex');
    currentFilter = { b: 100, c: 100, custom: '' };

    originalImageFile = file;
    originalImageURL = URL.createObjectURL(file);
    initCropperWithImage(originalImageURL);
    goToStep(2);
}

function initCropperWithImage(src) {
    const cropperImage = document.getElementById("cropper-image");
    cropperImage.src = src;
    cropperImage.classList.remove("hidden");
    currentRotation = 0;

    if (currentCropper) {
        currentCropper.destroy();
    }

    currentCropper = new Cropper(cropperImage, {
        aspectRatio: selectedSize.widthMm / selectedSize.heightMm,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 0.8,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        ready: function () {
            applyEnhancement();
        }
    });
}

function rotateLeft() {
    if (currentCropper) currentCropper.rotate(-90);
}

function rotateRight() {
    if (currentCropper) currentCropper.rotate(90);
}

function updateProgress(message, percent) {
    const toast = document.getElementById('progress-toast');
    if (toast) {
        toast.classList.remove('translate-y-24', 'opacity-0');
        document.getElementById('progress-message').textContent = message;
        document.getElementById('progress-bar').style.width = `${percent}%`;
        document.getElementById('progress-percent').textContent = `${percent}%`;
    }
}

function hideProgress() {
    const toast = document.getElementById('progress-toast');
    if (toast) {
        toast.classList.add('translate-y-24', 'opacity-0');
        setTimeout(() => {
            document.getElementById('progress-bar').style.width = '0%';
            document.getElementById('progress-percent').textContent = '0%';
        }, 300);
    }
}

async function autoFramePasfoto() {
    if (!currentCropper) return;

    updateProgress("Memeriksa AI Pendeteksi...", 10);

    if (!isFaceApiLoaded) {
        updateProgress("Memuat Model AI...", 30);
        await initFaceAPI();
        if (!isFaceApiLoaded) {
            hideProgress();
            showCustomAlert('Gagal memuat AI Pendeteksi Wajah. Periksa koneksi Anda.');
            return;
        }
    }

    const imageEl = document.getElementById('cropper-image');

    updateProgress("Menganalisis Wajah...", 60);

    try {
        const detection = await faceapi.detectSingleFace(imageEl, new faceapi.TinyFaceDetectorOptions());
        updateProgress("Menghitung Proporsi Pasfoto...", 85);

        if (detection) {
            const { x, y, width, height } = detection.box;
            const aspectRatio = selectedSize.widthMm / selectedSize.heightMm;

            const targetHeight = height / 0.42;
            const targetWidth = targetHeight * aspectRatio;

            const targetX = x + (width / 2) - (targetWidth / 2);

            const headroom = targetHeight * 0.15;
            const targetY = y - headroom;

            currentCropper.setData({
                x: targetX,
                y: targetY,
                width: targetWidth,
                height: targetHeight
            });

            updateProgress("Selesai!", 100);
            setTimeout(hideProgress, 1200);
        } else {
            hideProgress();
            showCustomAlert('Wajah tidak terdeteksi dengan jelas. Silakan sesuaikan bingkai secara manual.');
        }
    } catch (error) {
        console.error(error);
        hideProgress();
        showCustomAlert('Terjadi kesalahan saat mendeteksi wajah.');
    }
}

async function toggleBgRemoval() {
    const toggle = document.getElementById('bg-remove-toggle');
    const loading = document.getElementById('bg-remove-loading');

    if (!originalImageFile) return;

    if (toggle.checked) {
        if (bgRemovedImageURL) {
            initCropperWithImage(bgRemovedImageURL);
            return;
        }
        loading.classList.remove('hidden');
        loading.classList.add('flex');
        toggle.disabled = true;

        try {
            const formData = new FormData();
            formData.append('image', originalImageFile);
            const response = await fetch("http://127.0.0.1:5000/remove-bg", {
                method: 'POST',
                body: formData
            });
            if (!response.ok) throw new Error('Server error');
            const blob = await response.blob();

            if (bgRemovedImageURL) URL.revokeObjectURL(bgRemovedImageURL);
            bgRemovedImageURL = URL.createObjectURL(blob);

            initCropperWithImage(bgRemovedImageURL);
        } catch (err) {
            showCustomAlert('Failed to remove background. Please make sure the server is running.');
            toggle.checked = false;
        } finally {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
            toggle.disabled = false;
        }
    } else {
        initCropperWithImage(originalImageURL);
    }
}

function applyFilter(type) {
    if (type === 'auto') { currentFilter.b = 110; currentFilter.c = 110; currentFilter.custom = ''; }
    else if (type === 'brighten') { currentFilter.b = 120; currentFilter.c = 100; currentFilter.custom = ''; }
    else if (type === 'contrast') { currentFilter.b = 100; currentFilter.c = 120; currentFilter.custom = ''; }
    else if (type === 'warm') { currentFilter.b = 100; currentFilter.c = 100; currentFilter.custom = 'sepia(40%) saturate(120%)'; }
    else if (type === 'cool') { currentFilter.b = 100; currentFilter.c = 100; currentFilter.custom = 'sepia(20%) hue-rotate(180deg) saturate(110%)'; }
    else if (type === 'bw') { currentFilter.b = 100; currentFilter.c = 100; currentFilter.custom = 'grayscale(100%)'; }
    applyEnhancement();
}

function resetEnhancements() {
    currentFilter = { b: 100, c: 100, custom: '' };
    applyEnhancement();
}

function applyEnhancement() {
    let filterStr = `brightness(${currentFilter.b}%) contrast(${currentFilter.c}%)`;
    if (currentFilter.custom) filterStr += ' ' + currentFilter.custom;

    const cropperContainer = document.querySelector('.cropper-container');
    if (cropperContainer) {
        const imgs = cropperContainer.querySelectorAll('img');
        imgs.forEach(img => img.style.filter = filterStr);
    }

    const cropperViewBox = document.querySelector('.cropper-view-box');
    if (cropperViewBox && selectedBgColor !== 'transparent') {
        cropperViewBox.style.backgroundColor = selectedBgColor;
    }
    updatePreviewLabel();
}

function initSizeCards() {
    const container = document.getElementById('size-cards-container');
    if (!container) return;

    container.innerHTML = sizes.map((size, index) => {
        const crops = confirmedCrops[index] || [];
        const isActive = index === activeSizeIndex;

        let thumbsHTML = '';
        if (crops.length > 0) {
            thumbsHTML = `<div class="mt-3 pt-3 border-t border-outline-variant/30 flex flex-wrap gap-2 items-start">
                ${crops.map((crop, ci) => `
                <div class="crop-thumbnail crop-thumb-enter flex flex-col items-center gap-1" style="animation-delay: ${ci * 0.05}s">
                    <div class="relative">
                        <img src="${crop.thumbURL}" class="rounded-md border border-outline-variant/30 object-cover" style="width: 40px; height: auto; max-height: 56px;" />
                        <button class="crop-delete-btn" onclick="event.stopPropagation(); removeCrop(${index}, ${ci})">
                            <span class="material-symbols-outlined" style="font-size:10px">close</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1 bg-surface-container rounded-full px-1 py-0.5">
                        <button onclick="event.stopPropagation(); changeCropQty(${index}, ${ci}, -1)" class="w-5 h-5 flex items-center justify-center rounded-full hover:bg-surface text-[11px]">-</button>
                        <span class="text-[11px] min-w-[16px] text-center font-semibold">${crop.quantity || 1}</span>
                        <button onclick="event.stopPropagation(); changeCropQty(${index}, ${ci}, 1)" class="w-5 h-5 flex items-center justify-center rounded-full hover:bg-surface text-[11px]">+</button>
                    </div>
                </div>
            `).join('')}
            </div>`;
        }

        const addBtnHTML = crops.length > 0
            ? `<button onclick="event.stopPropagation(); addMoreCrop(${index})" class="flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wider text-primary hover:text-primary/80 transition-colors px-2 py-1 rounded-lg hover:bg-surface-container"><span class="material-symbols-outlined text-[14px]">add</span> Add</button>`
            : '';

        const hintHTML = isActive && crops.length === 0
            ? `<div class="mt-3 pt-3 border-t border-outline-variant/30 flex items-center gap-2 text-primary"><span class="material-symbols-outlined text-[16px]">crop</span><span class="text-[12px] font-medium">Atur bingkai lalu klik Confirm</span></div>`
            : '';

        const countHTML = crops.length > 0
            ? `<div class="mt-2 flex items-center gap-1 text-[11px] text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">photo_library</span>${crops.length} crop${crops.length > 1 ? 's' : ''} confirmed</div>`
            : '';

        return `
        <article class="size-card min-h-0 flex flex-col rounded-xl p-4 border transition-all duration-200 overflow-hidden relative cursor-pointer ${isActive ? 'bg-surface-container-lowest border-primary ring-2 ring-primary/20' : 'bg-surface-container-lowest border-outline-variant/60 hover:border-outline-variant'}" onclick="selectSize(${index})">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-surface-container rounded flex items-center justify-center shrink-0 border border-outline-variant/30"><span class="font-bold text-xs">${size.label}</span></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="font-headline-md text-[18px] leading-snug text-on-surface">${size.label} Print</h3>
                        ${addBtnHTML}
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">${size.desc}</p>
                </div>
            </div>
            ${hintHTML}
            ${thumbsHTML}
            ${countHTML}
        </article>`;
    }).join('');

    const total = Object.values(confirmedCrops).reduce((sum, arr) => sum + arr.length, 0);
    const totalQtyEl = document.getElementById('total-qty');
    if (totalQtyEl) totalQtyEl.textContent = total + " Prints";
}

function selectSize(index) {
    activeSizeIndex = index;
    selectedSize = sizes[index];
    if (currentCropper) {
        currentCropper.setAspectRatio(selectedSize.widthMm / selectedSize.heightMm);
    }
    initSizeCards();
    updatePreviewLabel();
    updateConfirmButton();
}

function addMoreCrop(index) {
    selectSize(index);
}

function confirmCrop() {
    if (!currentCropper || activeSizeIndex === null) return;
    const sz = sizes[activeSizeIndex];

    const fullCanvas = getFinalCanvas(sz);
    if (!fullCanvas) return;

    fullCanvas.toBlob((fullBlob) => {
        const dataURL = URL.createObjectURL(fullBlob);

        const thumbW = 80;
        const thumbH = Math.round(thumbW * sz.heightMm / sz.widthMm);
        const thumbCrop = currentCropper.getCroppedCanvas({ width: thumbW, height: thumbH });
        const thumbFinal = document.createElement('canvas');
        thumbFinal.width = thumbW;
        thumbFinal.height = thumbH;
        const tctx = thumbFinal.getContext('2d');

        if (selectedBgColor !== 'transparent') {
            tctx.fillStyle = selectedBgColor;
            tctx.fillRect(0, 0, thumbW, thumbH);
        }

        let filterStr = `brightness(${currentFilter.b}%) contrast(${currentFilter.c}%)`;
        if (currentFilter.custom) filterStr += ' ' + currentFilter.custom;
        tctx.filter = filterStr;
        tctx.drawImage(thumbCrop, 0, 0);

        thumbFinal.toBlob((thumbBlob) => {
            const thumbURL = URL.createObjectURL(thumbBlob);

            if (!confirmedCrops[activeSizeIndex]) confirmedCrops[activeSizeIndex] = [];
            confirmedCrops[activeSizeIndex].push({
                id: 'crop_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                dataURL: dataURL,
                thumbURL: thumbURL,
                widthMm: sz.widthMm,
                heightMm: sz.heightMm,
                sizeLabel: sz.label,
                quantity: 1
            });

            initSizeCards();
            triggerConfirmAnimation();
        }, 'image/png');

    }, 'image/png');
}

function triggerConfirmAnimation() {
    const btn = document.getElementById('confirm-crop-btn');
    if (btn) {
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">done_all</span> Added!';
        btn.classList.remove('confirm-pulse');
        btn.classList.add('bg-green-700');
        setTimeout(() => {
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span> Confirm';
            btn.classList.remove('bg-green-700');
            btn.classList.add('confirm-pulse');
        }, 800);
    }
    const container = document.getElementById('size-cards-container');
    const cards = container.querySelectorAll('.size-card');
    if (cards[activeSizeIndex]) {
        cards[activeSizeIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function removeCrop(sizeIndex, cropIndex) {
    if (confirmedCrops[sizeIndex]) {
        const removedCrop = confirmedCrops[sizeIndex][cropIndex];

        if (removedCrop.dataURL) URL.revokeObjectURL(removedCrop.dataURL);
        if (removedCrop.thumbURL) URL.revokeObjectURL(removedCrop.thumbURL);

        confirmedCrops[sizeIndex].splice(cropIndex, 1);
        if (confirmedCrops[sizeIndex].length === 0) {
            delete confirmedCrops[sizeIndex];
        }
    }
    initSizeCards();
}

function changeCropQty(sizeIndex, cropIndex, delta) {
    const crop = confirmedCrops[sizeIndex][cropIndex];
    if (!crop.quantity) crop.quantity = 1;
    crop.quantity += delta;
    if (crop.quantity < 1) crop.quantity = 1;
    initSizeCards();
}

function updateConfirmButton() {
    const btn = document.getElementById('confirm-crop-btn');
    if (btn && currentCropper) {
        btn.classList.remove('hidden');
    }
}

function selectBgColor(color, btnElement) {
    selectedBgColor = color;
    document.querySelectorAll('.bg-color-btn .bg-check').forEach(chk => chk.classList.add('hidden'));
    const check = btnElement.querySelector('.bg-check');
    if (check) check.classList.remove('hidden');

    const cropperViewBox = document.querySelector('.cropper-view-box');
    if (cropperViewBox) {
        cropperViewBox.style.backgroundColor = color === 'transparent' ? 'transparent' : color;
    }
    updatePreviewLabel();
}

function getFinalCanvas(sizeOverride = null) {
    if (!currentCropper) return null;
    const sz = sizeOverride || selectedSize;
    const dpiMod = 11.811;
    const outWidth = Math.round(sz.widthMm * dpiMod);
    const outHeight = Math.round(sz.heightMm * dpiMod);

    const cropCanvas = currentCropper.getCroppedCanvas({ width: outWidth, height: outHeight });
    if (!cropCanvas) return null;

    if (selectedBgColor === 'transparent' && currentFilter.b === 100 && currentFilter.c === 100 && !currentFilter.custom) {
        return cropCanvas;
    }

    const finalCanvas = document.createElement('canvas');
    finalCanvas.width = outWidth;
    finalCanvas.height = outHeight;
    const ctx = finalCanvas.getContext('2d', { willReadFrequently: true });

    if (selectedBgColor !== 'transparent') {
        ctx.fillStyle = selectedBgColor;
        ctx.fillRect(0, 0, outWidth, outHeight);
    }

    let filterStr = `brightness(${currentFilter.b}%) contrast(${currentFilter.c}%)`;
    if (currentFilter.custom) filterStr += ' ' + currentFilter.custom;
    ctx.filter = filterStr;
    ctx.drawImage(cropCanvas, 0, 0);

    return finalCanvas;
}

function updatePreviewLabel() {
    const label = document.getElementById('preview-size-label');
    if (label) label.textContent = "Editing: " + selectedSize.label;
}

function handleDownload() {
    if (!currentCropper) return;
    const canvas = getFinalCanvas();
    if (canvas) {
        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = `turikale-print-${selectedSize.label}.png`;
            link.href = url;
            link.click();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        }, 'image/png');
    }
}

function autoFillPaper() {
            const canvas = document.getElementById('paper-canvas');
            const existingPhotos = Array.from(canvas.querySelectorAll('.draggable-photo'));

            const desiredCrops = {};
            Object.values(confirmedCrops).forEach(cropsArray => {
                cropsArray.forEach(crop => {
                    desiredCrops[crop.id] = crop;
                });
            });

            existingPhotos.forEach(photoEl => {
                const id = photoEl.getAttribute('data-crop-id');
                if (id && id !== 'manual' && !desiredCrops[id]) {
                    photoEl.remove();
                }
            });

            Object.values(desiredCrops).forEach(crop => {
                const currentElements = Array.from(canvas.querySelectorAll(`.draggable-photo[data-crop-id="${crop.id}"]`));
                let currentQty = currentElements.length;
                const targetQty = crop.quantity || 1;

                while (currentQty < targetQty) {
                    const offset = (canvas.querySelectorAll('.draggable-photo').length % 5) * 20 + 20;
                    const photoEl = createPhotoElement(crop.dataURL, crop.widthMm, crop.heightMm, offset, offset, crop.id);
                    canvas.appendChild(photoEl);
                    currentQty++;
                }

                while (currentQty > targetQty) {
                    const elToRemove = currentElements.pop();
                    if (elToRemove) elToRemove.remove();
                    currentQty--;
                }
            });

            updatePrintQtyLabel();
            updateCutGuide();
        }

function autoArrangePhotos() {
    const canvas = document.getElementById('paper-canvas');
    const photos = [...canvas.querySelectorAll('.draggable-photo')];
    if (!photos.length) return;

    const landscapePhotos = [];
    const portraitPhotos = [];

    photos.forEach(photo => {
        const size = getVisualSize(photo);
        if (size.isLandscape) landscapePhotos.push(photo);
        else portraitPhotos.push(photo);
    });

    const sortByArea = (a, b) => {
        const sa = getVisualSize(a);
        const sb = getVisualSize(b);
        return (sb.width * sb.height) - (sa.width * sa.height);
    };

    landscapePhotos.sort(sortByArea);
    portraitPhotos.sort(sortByArea);

    const orderedPhotos = [...landscapePhotos, ...portraitPhotos];
    const paperWidth = canvas.clientWidth;
    const gap = 10;
    const safePadding = 20;

    let currentX = safePadding;
    let currentY = safePadding;
    let rowHeight = 0;

    orderedPhotos.forEach(photo => {
        const size = getVisualSize(photo);
        if (currentX + size.width > paperWidth - safePadding) {
            currentX = safePadding;
            currentY += rowHeight + gap;
            rowHeight = 0;
        }

        let visualOffsetX = size.isLandscape ? (size.width - photo.offsetWidth) / 2 : 0;
        photo.style.left = `${currentX + visualOffsetX}px`;
        photo.style.top = `${currentY}px`;

        updatePopupPosition(photo);

        currentX += size.width + gap;
        rowHeight = Math.max(rowHeight, size.height);
    });

    updateCutGuide();
}

function getVisualSize(element) {
    const rotation = parseInt(element.getAttribute('data-rotation') || '0');
    let width = element.offsetWidth;
    let height = element.offsetHeight;
    const isLandscape = rotation === 90 || rotation === 270;

    if (isLandscape) {
        [width, height] = [height, width];
    }
    return { width, height, isLandscape };
}

function updatePrintQtyLabel() {
    const count = document.querySelectorAll('#paper-canvas .draggable-photo').length;
    document.getElementById('print-qty-label').textContent = count + " Photos";
}

function zoomPaper(delta) {
    currentZoom = Math.max(0.5, Math.min(2.0, currentZoom + delta));
    document.getElementById('zoom-level').innerText = `${Math.round(currentZoom * 100)}%`;
    document.getElementById('paper-workspace').style.transform = `scale(${currentZoom})`;
}

function updatePaperSize() {
    const select = document.getElementById('paper-size-select');
    const option = select.options[select.selectedIndex];
    const canvas = document.getElementById('paper-canvas');
    canvas.style.width = `${option.getAttribute('data-width')}mm`;
    canvas.style.height = `${option.getAttribute('data-height')}mm`;
}

function deselectAllPhotos(e) {
    if (e && e.target.closest('.draggable-photo') || e && e.target.closest('.photo-controls')) return;
    document.querySelectorAll('.draggable-photo').forEach(el => el.classList.remove('selected'));
}

function updatePopupPosition(element) {
    const controls = element.querySelector('.photo-controls');
    if (!controls) return;

    const top = parseFloat(element.style.top) || 0;
    const left = parseFloat(element.style.left) || 0;

    const parent = element.parentElement;
    if(!parent) return;

    const canvasHeight = parent.clientHeight;
    const canvasWidth = parent.clientWidth;
    const elHeight = element.offsetHeight;
    const elWidth = element.offsetWidth;

    if (top < 45) {
        controls.style.top = 'auto';
        if (top + elHeight + 45 > canvasHeight) {
            controls.style.bottom = '4px';
        } else {
            controls.style.bottom = '-40px';
        }
    } else {
        controls.style.top = '-40px';
        controls.style.bottom = 'auto';
    }

    if (left < 50) {
        controls.style.left = '0';
        controls.style.right = 'auto';
        controls.style.transform = 'none';
    } else if (left + elWidth + 50 > canvasWidth) {
        controls.style.left = 'auto';
        controls.style.right = '0';
        controls.style.transform = 'none';
    } else {
        controls.style.left = '50%';
        controls.style.right = 'auto';
        controls.style.transform = 'translateX(-50%)';
    }
}

function makeInteractive(element) {
    let isDragging = false;
    let startX, startY;
    let initialPositions = [];
    const guideV = document.getElementById('guide-v');
    const guideH = document.getElementById('guide-h');

    element.ondragstart = () => false;

    function getVisualBounds(el, cssLeft, cssTop) {
        const rot = parseInt(el.getAttribute('data-rotation') || '0', 10);
        const isLandscape = rot === 90 || rot === 270;

        const w = el.offsetWidth;
        const h = el.offsetHeight;
        const cx = cssLeft + w / 2;
        const cy = cssTop + h / 2;
        const vW = isLandscape ? h : w;
        const vH = isLandscape ? w : h;

        return {
            left: cx - vW / 2,
            top: cy - vH / 2,
            vW: vW,
            vH: vH,
            w: w,
            h: h
        };
    }

    element.onpointerdown = (e) => {
        if (e.target.closest('.photo-controls')) return;

        const isMultiSelect = e.shiftKey || e.ctrlKey || e.metaKey;

        if (isMultiSelect) {
            element.classList.toggle('selected');
        } else {
            if (!element.classList.contains('selected')) {
                deselectAllPhotos();
                element.classList.add('selected');
            }
        }

        if (!element.classList.contains('selected')) return;

        isDragging = true;
        element.setPointerCapture(e.pointerId);
        startX = e.clientX;
        startY = e.clientY;

        initialPositions = [];
        document.querySelectorAll('.draggable-photo.selected').forEach(el => {
            initialPositions.push({
                el: el,
                initialLeft: parseFloat(el.style.left) || 0,
                initialTop: parseFloat(el.style.top) || 0
            });
            el.parentNode.appendChild(el);
        });

        updatePopupPosition(element);
    };

    element.onpointermove = (e) => {
        if (!isDragging) return;

        const dx = (e.clientX - startX) / currentZoom;
        const dy = (e.clientY - startY) / currentZoom;

        const primaryInitial = initialPositions.find(p => p.el === element);
        if (!primaryInitial) return;

        let primaryNewLeft = primaryInitial.initialLeft + dx;
        let primaryNewTop = primaryInitial.initialTop + dy;

        const unselectedPhotos = [...document.querySelectorAll('.draggable-photo')]
            .filter(p => !p.classList.contains('selected'));

        let snapX = false;
        let snapY = false;

        let dragBounds = getVisualBounds(element, primaryNewLeft, primaryNewTop);

        unselectedPhotos.forEach(other => {
            const otherLeft = parseFloat(other.style.left) || 0;
            const otherTop = parseFloat(other.style.top) || 0;
            const otherBounds = getVisualBounds(other, otherLeft, otherTop);

            if (Math.abs(dragBounds.left - otherBounds.left) < 8) {
                primaryNewLeft = otherBounds.left + dragBounds.vW / 2 - dragBounds.w / 2;
                snapX = true;
                guideV.style.display = 'block';
                guideV.style.left = `${otherBounds.left}px`;
            }

            if (Math.abs(dragBounds.top - otherBounds.top) < 8) {
                primaryNewTop = otherBounds.top + dragBounds.vH / 2 - dragBounds.h / 2;
                snapY = true;
                guideH.style.display = 'block';
                guideH.style.top = `${otherBounds.top}px`;
            }
        });

        if (!snapX) guideV.style.display = 'none';
        if (!snapY) guideH.style.display = 'none';

        const finalDx = primaryNewLeft - primaryInitial.initialLeft;
        const finalDy = primaryNewTop - primaryInitial.initialTop;

        initialPositions.forEach(pos => {
            pos.el.style.left = `${pos.initialLeft + finalDx}px`;
            pos.el.style.top = `${pos.initialTop + finalDy}px`;
            updatePopupPosition(pos.el);
        });
    };

    const stopDrag = (e) => {
        if (!isDragging) return;
        isDragging = false;
        guideV.style.display = 'none';
        guideH.style.display = 'none';
        element.releasePointerCapture(e.pointerId);
    };

    element.onpointerup = stopDrag;
    element.onpointercancel = stopDrag;
}

function createPhotoElement(src, wMm, hMm, x = 20, y = 20, cropId = 'manual') {
    const screenPxPerMm = 3.779;
    const container = document.createElement('div');
    container.className = 'draggable-photo';
    container.setAttribute('data-crop-id', cropId);
    container.style.width = `${wMm * screenPxPerMm}px`;
    container.style.height = `${hMm * screenPxPerMm}px`;
    container.style.left = `${x}px`;
    container.style.top = `${y}px`;
    container.innerHTML = `
    <img src="${src}" class="w-full h-full object-cover pointer-events-none" />
    <div class="photo-controls">
        <button onclick="rotatePhoto(this.parentElement.parentElement)" class="p-1 hover:bg-slate-600 rounded text-slate-200 flex items-center justify-center transition-colors" title="Rotasi 90&deg;"><span class="material-symbols-outlined text-[16px]">rotate_right</span></button>
        <button onclick="duplicatePhoto(this.parentElement.parentElement)" class="p-1 hover:bg-slate-600 rounded text-slate-200 flex items-center justify-center transition-colors" title="Duplikat"><span class="material-symbols-outlined text-[16px]">content_copy</span></button>
        <button onclick="deletePhoto(this.parentElement.parentElement)" class="p-1 hover:bg-red-500 rounded text-slate-200 flex items-center justify-center transition-colors" title="Hapus"><span class="material-symbols-outlined text-[16px]">delete</span></button>
    </div>`;
    makeInteractive(container);
    return container;
}

function addPhotoToPaper() {
    if (!currentCropper) return;
    const canvas = getFinalCanvas();
    if (!canvas) return;

    canvas.toBlob((blob) => {
        const src = URL.createObjectURL(blob);
        const offset = (document.getElementById('paper-canvas').children.length % 5) * 20 + 20;
        const photoEl = createPhotoElement(src, selectedSize.widthMm, selectedSize.heightMm, offset, offset);
        document.getElementById('paper-canvas').appendChild(photoEl);
        deselectAllPhotos();
        photoEl.classList.add('selected');
        updateCutGuide();
        updatePrintQtyLabel();
    });
}

function duplicatePhoto(element) {
    const container = document.createElement('div');
    container.className = 'draggable-photo';
    container.setAttribute('data-crop-id', 'manual');
    container.style.width = element.style.width;
    container.style.height = element.style.height;
    container.style.left = `${parseFloat(element.style.left) + 20}px`;
    container.style.top = `${parseFloat(element.style.top) + 20}px`;
    container.innerHTML = element.innerHTML;
    const rot = element.getAttribute('data-rotation');
    if (rot && rot !== '0') {
        container.setAttribute('data-rotation', rot);
        container.style.transform = `rotate(${rot}deg)`;
    }
    makeInteractive(container);
    document.getElementById('paper-canvas').appendChild(container);
    deselectAllPhotos();
    container.classList.add('selected');

    updatePopupPosition(container);

    updateCutGuide();
    updatePrintQtyLabel();
}

function rotatePhoto(element) {
    let rot = parseInt(element.getAttribute('data-rotation') || '0', 10);
    rot = (rot + 90) % 360;
    element.setAttribute('data-rotation', rot);
    element.style.transform = `rotate(${rot}deg)`;
    element.style.transformOrigin = 'center center';

    updatePopupPosition(element);
}

function deletePhoto(element) {
    const cropId = element.getAttribute('data-crop-id');

    if (cropId && cropId !== 'manual') {
        for (const sizeIndex in confirmedCrops) {
            const cropsArray = confirmedCrops[sizeIndex];
            const cropIndex = cropsArray.findIndex(c => c.id === cropId);

            if (cropIndex !== -1) {
                cropsArray[cropIndex].quantity -= 1;

                if (cropsArray[cropIndex].quantity <= 0) {
                    const removedCrop = cropsArray.splice(cropIndex, 1)[0];
                    if (removedCrop.dataURL) URL.revokeObjectURL(removedCrop.dataURL);
                    if (removedCrop.thumbURL) URL.revokeObjectURL(removedCrop.thumbURL);
                }

                if (cropsArray.length === 0) {
                    delete confirmedCrops[sizeIndex];
                }

                break;
            }
        }

        initSizeCards();
    }

    element.remove();
    updatePrintQtyLabel();
}

function updateCutGuide() {
    const enabled = document.getElementById('cut-guide-toggle').checked;
    const marginVal = parseFloat(document.getElementById('cut-guide-margin').value);
    const lineVal = parseFloat(document.getElementById('cut-guide-line').value);
    document.querySelectorAll('#paper-canvas .draggable-photo').forEach(el => {
        if (enabled && (marginVal > 0 || lineVal > 0)) {
            el.style.border = `${marginVal}px solid #ffffff`;
            el.style.outline = lineVal > 0 ? `${lineVal}px solid rgba(0,0,0,0.1)` : 'none';
            el.style.boxShadow = 'none';
        } else {
            el.style.border = 'none';
            el.style.outline = 'none';
            el.style.boxShadow = '0 0 0 2px #ffffff, 0 0 0 2.5px rgba(0,0,0,0.15)';
        }
    });
}

function applyPrintCutGuide() {
    const toggle = document.getElementById('cut-guide-toggle');
    if (!toggle || !toggle.checked) return '#paper-canvas .draggable-photo { border: none !important; outline: none !important; }';
    const marginVal = parseFloat(document.getElementById('cut-guide-margin').value);
    const lineVal = parseFloat(document.getElementById('cut-guide-line').value);
    return `#paper-canvas .draggable-photo { border: ${marginVal}px solid #ffffff !important; outline: ${lineVal > 0 ? `${lineVal}px solid rgba(0,0,0,0.1)!important` : 'none !important'}; box-shadow: none !important; }`;
}

function executePrint() {
    deselectAllPhotos();
    const canvas = document.getElementById('paper-canvas');
    if (canvas.querySelectorAll('.draggable-photo').length === 0) {
        showCustomAlert('Please add at least one photo to print.');
        return;
    }
    const printCutStyle = document.createElement('style');
    printCutStyle.id = 'print-cut-guide-style';
    printCutStyle.textContent = `@media print { ${applyPrintCutGuide()} }`;
    document.head.appendChild(printCutStyle);

    setTimeout(() => {
        window.print();
        printCutStyle.remove();
    }, 100);
}

const dropZone = document.getElementById('drop-zone');
if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
    ['dragenter', 'dragover'].forEach(ev => dropZone.addEventListener(ev, () => document.getElementById('drop-zone-overlay').classList.replace('opacity-0', 'opacity-100'), false));
    ['dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, () => document.getElementById('drop-zone-overlay').classList.replace('opacity-100', 'opacity-0'), false));
    dropZone.addEventListener('drop', e => {
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0 && e.dataTransfer.files[0].type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            const input = document.getElementById('photo-upload');
            input.files = dt.files;
            handleImageUpload({ target: input });
        }
    }, false);
}

const paperCanvas = document.getElementById('paper-canvas');
let selectionBox = null;
let selectionStartX = 0;
let selectionStartY = 0;

paperCanvas.addEventListener('pointerdown', (e) => {
    if (e.target !== paperCanvas) return;

    if (!e.shiftKey && !e.ctrlKey) {
        deselectAllPhotos();
    }

    selectionStartX = e.clientX;
    selectionStartY = e.clientY;

    selectionBox = document.createElement('div');
    selectionBox.className = 'selection-box';

    const rect = paperCanvas.getBoundingClientRect();
    const left = (e.clientX - rect.left) / currentZoom;
    const top = (e.clientY - rect.top) / currentZoom;

    selectionBox.style.left = `${left}px`;
    selectionBox.style.top = `${top}px`;
    selectionBox.style.width = '0px';
    selectionBox.style.height = '0px';

    paperCanvas.appendChild(selectionBox);
});

paperCanvas.addEventListener('pointermove', (e) => {
    if (!selectionBox) return;

    const rect = paperCanvas.getBoundingClientRect();
    const currentX = e.clientX;
    const currentY = e.clientY;

    const left = (Math.min(currentX, selectionStartX) - rect.left) / currentZoom;
    const top = (Math.min(currentY, selectionStartY) - rect.top) / currentZoom;
    const width = Math.abs(currentX - selectionStartX) / currentZoom;
    const height = Math.abs(currentY - selectionStartY) / currentZoom;

    selectionBox.style.left = `${left}px`;
    selectionBox.style.top = `${top}px`;
    selectionBox.style.width = `${width}px`;
    selectionBox.style.height = `${height}px`;

    const boxRect = { left, top, right: left + width, bottom: top + height };

    document.querySelectorAll('.draggable-photo').forEach(photo => {
        const pLeft = parseFloat(photo.style.left);
        const pTop = parseFloat(photo.style.top);
        const pRight = pLeft + photo.offsetWidth;
        const pBottom = pTop + photo.offsetHeight;

        if (
            boxRect.left < pRight &&
            boxRect.right > pLeft &&
            boxRect.top < pBottom &&
            boxRect.bottom > pTop
        ) {
            photo.classList.add('selected');
        } else if (!e.shiftKey && !e.ctrlKey) {
            photo.classList.remove('selected');
        }
    });
});

window.addEventListener('pointerup', () => {
    if (selectionBox) {
        selectionBox.remove();
        selectionBox = null;
    }
});

window.addEventListener('keydown', (e) => {
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;

    if (currentStep !== 3) return;

    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
        e.preventDefault();

        const allPhotos = document.querySelectorAll('#paper-canvas .draggable-photo');
        if (allPhotos.length > 0) {
            allPhotos.forEach(photo => photo.classList.add('selected'));
        }
    }

    if (e.key === 'Delete' || e.key === 'Backspace') {
        const selectedPhotos = document.querySelectorAll('.draggable-photo.selected');

        if (selectedPhotos.length > 0) {
            e.preventDefault();

            selectedPhotos.forEach(photo => {
                deletePhoto(photo);
            });

            const guideV = document.getElementById('guide-v');
            const guideH = document.getElementById('guide-h');
            if(guideV) guideV.style.display = 'none';
            if(guideH) guideH.style.display = 'none';
        }
    }
});

initSizeCards();

window.addEventListener("load", function () {
    setTimeout(() => {
        const preloader = document.getElementById("preloader");
        if (preloader) {
            preloader.style.opacity = "0";
            preloader.style.transition = "0.5s";
            setTimeout(() => preloader.style.display = "none", 500);
        }
    }, 1000);
});

function showCustomAlert(message, type = 'error') {
    document.getElementById('custom-alert-message').textContent = message;
    document.getElementById('custom-alert-modal').classList.remove('opacity-0', 'pointer-events-none');
    document.getElementById('custom-alert-content').classList.remove('scale-95');
}

function closeCustomAlert() {
    document.getElementById('custom-alert-modal').classList.add('opacity-0', 'pointer-events-none');
    document.getElementById('custom-alert-content').classList.add('scale-95');
}
