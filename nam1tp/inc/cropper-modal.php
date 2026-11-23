<div id="tpCropperModal" class="fixed inset-0 z-[9999] bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl sm:rounded-3xl max-w-xl w-full flex flex-col shadow-2xl overflow-hidden max-h-[95vh] animate-in fade-in duration-200">

        <div class="px-5 py-4 border-b border-[#1e2d3d] flex items-center justify-between bg-[#101822]">
            <div class="flex items-center gap-2.5">
                <div class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">crop</span>
                </div>
                <div>
                    <h3 id="tpCropperTitle" class="text-sm font-bold text-white">Sesuaikan & Potong Foto</h3>
                    <p id="tpCropperSubtitle" class="text-[11px] text-slate-400">Geser untuk memposisikan dan gunakan slider untuk memperbesar foto</p>
                </div>
            </div>
            <button type="button" onclick="closeImageCropper()" class="size-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/60 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="p-4 sm:p-5 flex-1 flex flex-col gap-3.5 overflow-y-auto custom-scrollbar">

            <div class="relative w-full h-[260px] sm:h-[320px] bg-[#090d12] rounded-xl overflow-hidden border border-[#1e2d3d] flex items-center justify-center">
                <img id="tpCropperImage" src="" alt="Crop Source" class="max-w-full max-h-full block">
            </div>

            <div class="bg-[#0b1017] border border-[#1a2636] rounded-xl p-3 space-y-2.5">
                <div class="flex items-center justify-between text-xs text-slate-300">
                    <span class="flex items-center gap-1.5 font-semibold text-[11px]">
                        <span class="material-symbols-outlined text-sm text-primary">zoom_in</span>
                        Perbesaran (Zoom)
                    </span>
                    <span id="tpCropperZoomValue" class="text-[11px] text-slate-400 font-mono">100%</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="tpCropperStepZoom(-0.1)" class="size-7 rounded-lg bg-[#162230] hover:bg-primary text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors shrink-0">
                        <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <input type="range" id="tpCropperZoomSlider" min="0.1" max="3" step="0.01" value="1" class="w-full h-1.5 bg-[#1e2d3d] rounded-lg appearance-none cursor-pointer accent-primary">
                    <button type="button" onclick="tpCropperStepZoom(0.1)" class="size-7 rounded-lg bg-[#162230] hover:bg-primary text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors shrink-0">
                        <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between gap-2 flex-wrap text-xs">
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="tpCropperRotate(-90)" title="Putar Kiri 90°" class="h-8 px-2.5 rounded-lg bg-[#0b1017] hover:bg-[#162230] text-slate-300 hover:text-white border border-[#1e2d3d] flex items-center gap-1 text-[11px] font-semibold transition-colors">
                        <span class="material-symbols-outlined text-sm">rotate_left</span>
                        <span>-90°</span>
                    </button>
                    <button type="button" onclick="tpCropperRotate(90)" title="Putar Kanan 90°" class="h-8 px-2.5 rounded-lg bg-[#0b1017] hover:bg-[#162230] text-slate-300 hover:text-white border border-[#1e2d3d] flex items-center gap-1 text-[11px] font-semibold transition-colors">
                        <span class="material-symbols-outlined text-sm">rotate_right</span>
                        <span>+90°</span>
                    </button>
                    <button type="button" onclick="tpCropperReset()" title="Reset Posisi & Zoom" class="h-8 px-2.5 rounded-lg bg-[#0b1017] hover:bg-[#162230] text-slate-300 hover:text-white border border-[#1e2d3d] flex items-center gap-1 text-[11px] font-semibold transition-colors">
                        <span class="material-symbols-outlined text-sm">restart_alt</span>
                        <span>Reset</span>
                    </button>
                </div>

                <div id="tpCropperShapeToggle" class="flex items-center gap-1 bg-[#0b1017] p-1 rounded-lg border border-[#1e2d3d]">
                    <button type="button" id="tpShapeCircleBtn" onclick="tpCropperSetShape('circle')" class="px-2.5 py-1 rounded text-[11px] font-semibold transition-all bg-primary text-white flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">circle</span>
                        <span>Bulat</span>
                    </button>
                    <button type="button" id="tpShapeSquareBtn" onclick="tpCropperSetShape('square')" class="px-2.5 py-1 rounded text-[11px] font-semibold transition-all text-slate-400 hover:text-white flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">square</span>
                        <span>Kotak</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-5 py-3.5 border-t border-[#1e2d3d] bg-[#101822] flex items-center justify-between">
            <span class="text-[11px] text-slate-400 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs text-emerald-400">pan_tool</span>
                Klik & geser untuk atur posisi
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeImageCropper()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition-colors">
                    Batal
                </button>
                <button type="button" onclick="tpCropperApply()" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold shadow-lg shadow-primary/25 flex items-center gap-1.5 transition-all">
                    <span class="material-symbols-outlined text-sm">check</span>
                    <span>Terapkan & Potong</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cropper-view-box,
.cropper-face {
    border-radius: 0;
}
.tp-cropper-circle .cropper-view-box,
.tp-cropper-circle .cropper-view-box img,
.tp-cropper-circle .cropper-face {
    border-radius: 50% !important;
}
.tp-cropper-circle .cropper-view-box {
    outline: 2px solid #136dec;
    outline-color: rgba(19, 109, 236, 0.9);
}
.cropper-modal {
    background-color: rgba(5, 8, 12, 0.8) !important;
}
</style>

<script>
let tpCropperInstance = null;
let tpActiveTargetInput = null;
let tpActivePreviewImg = null;
let tpActiveOptions = {};
let tpCurrentFileName = 'cropped.jpg';

function openImageCropper(file, targetInput, options = {}) {
    if (!file || !file.type.startsWith('image/')) return;
    tpActiveTargetInput = targetInput;
    tpActiveOptions = options;
    tpCurrentFileName = file.name || 'cropped.jpg';

    const modal = document.getElementById('tpCropperModal');
    const imageEl = document.getElementById('tpCropperImage');
    const titleEl = document.getElementById('tpCropperTitle');
    const shapeToggle = document.getElementById('tpCropperShapeToggle');

    if (options.title) {
        titleEl.innerText = options.title;
    } else {
        titleEl.innerText = 'Sesuaikan & Potong Foto';
    }

    const defaultShape = options.shape || 'square';
    if (options.aspectRatio && options.aspectRatio !== 1) {
        if (shapeToggle) shapeToggle.style.display = 'none';
    } else {
        if (shapeToggle) shapeToggle.style.display = 'flex';
        tpCropperSetShape(defaultShape, false);
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        imageEl.src = e.target.result;
        modal.classList.remove('hidden');

        if (tpCropperInstance) {
            tpCropperInstance.destroy();
            tpCropperInstance = null;
        }

        let ratio = NaN;
        if (options.aspectRatio === '1:1' || options.aspectRatio === 1) ratio = 1;
        else if (options.aspectRatio === '16:9') ratio = 16 / 9;
        else if (options.aspectRatio === '9:16') ratio = 9 / 16;
        else if (options.aspectRatio === '3:4') ratio = 3 / 4;
        else if (options.aspectRatio === '4:3') ratio = 4 / 3;
        else if (typeof options.aspectRatio === 'number') ratio = options.aspectRatio;

        setTimeout(() => {
            tpCropperInstance = new Cropper(imageEl, {
                aspectRatio: ratio,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                zoomOnWheel: true,
                zoomOnTouch: true,
                ready: function() {
                    const slider = document.getElementById('tpCropperZoomSlider');
                    if (slider) slider.value = 1;
                    document.getElementById('tpCropperZoomValue').innerText = '100%';
                    if (options.shape === 'circle') {
                        tpCropperSetShape('circle', false);
                    }
                },
                zoom: function(e) {
                    const ratio = e.detail.ratio;
                    const slider = document.getElementById('tpCropperZoomSlider');
                    if (slider) {
                        slider.value = Math.max(0.1, Math.min(3, ratio));
                        document.getElementById('tpCropperZoomValue').innerText = Math.round(ratio * 100) + '%';
                    }
                }
            });
        }, 80);
    };
    reader.readAsDataURL(file);
}

function closeImageCropper() {
    const modal = document.getElementById('tpCropperModal');
    if (modal) modal.classList.add('hidden');
    if (tpCropperInstance) {
        tpCropperInstance.destroy();
        tpCropperInstance = null;
    }
}

function tpCropperStepZoom(step) {
    if (!tpCropperInstance) return;
    tpCropperInstance.zoom(step);
}

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('tpCropperZoomSlider');
    if (slider) {
        slider.addEventListener('input', function() {
            if (!tpCropperInstance) return;
            const targetVal = parseFloat(this.value);
            tpCropperInstance.zoomTo(targetVal);
            document.getElementById('tpCropperZoomValue').innerText = Math.round(targetVal * 100) + '%';
        });
    }

    document.querySelectorAll('input[type="file"][data-crop]').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files && this.files[0];
            if (!file) return;

            const opts = {
                aspectRatio: this.getAttribute('data-crop') || 1,
                shape: this.getAttribute('data-crop-shape') || 'square',
                title: this.getAttribute('data-crop-title') || 'Sesuaikan Foto',
                previewId: this.getAttribute('data-preview-id') || null
            };
            openImageCropper(file, this, opts);
        });
    });
});

function tpCropperRotate(degree) {
    if (!tpCropperInstance) return;
    tpCropperInstance.rotate(degree);
}

function tpCropperReset() {
    if (!tpCropperInstance) return;
    tpCropperInstance.reset();
    const slider = document.getElementById('tpCropperZoomSlider');
    if (slider) slider.value = 1;
    document.getElementById('tpCropperZoomValue').innerText = '100%';
}

function tpCropperSetShape(shape, updateClasses = true) {
    const modal = document.getElementById('tpCropperModal');
    const circleBtn = document.getElementById('tpShapeCircleBtn');
    const squareBtn = document.getElementById('tpShapeSquareBtn');

    if (shape === 'circle') {
        modal.classList.add('tp-cropper-circle');
        if (circleBtn && squareBtn) {
            circleBtn.className = 'px-2.5 py-1 rounded text-[11px] font-semibold transition-all bg-primary text-white flex items-center gap-1';
            squareBtn.className = 'px-2.5 py-1 rounded text-[11px] font-semibold transition-all text-slate-400 hover:text-white flex items-center gap-1';
        }
    } else {
        modal.classList.remove('tp-cropper-circle');
        if (circleBtn && squareBtn) {
            circleBtn.className = 'px-2.5 py-1 rounded text-[11px] font-semibold transition-all text-slate-400 hover:text-white flex items-center gap-1';
            squareBtn.className = 'px-2.5 py-1 rounded text-[11px] font-semibold transition-all bg-primary text-white flex items-center gap-1';
        }
    }
}

function tpCropperApply() {
    if (!tpCropperInstance || !tpActiveTargetInput) return;

    let targetWidth = 1200;
    let targetHeight = 1200;
    if (tpActiveOptions.aspectRatio === '16:9') {
        targetWidth = 1920;
        targetHeight = 1080;
    } else if (tpActiveOptions.aspectRatio === '9:16') {
        targetWidth = 1080;
        targetHeight = 1920;
    } else if (tpActiveOptions.aspectRatio === '3:4') {
        targetWidth = 900;
        targetHeight = 1200;
    }

    const canvas = tpCropperInstance.getCroppedCanvas({
        maxWidth: targetWidth,
        maxHeight: targetHeight,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });

    if (!canvas) {
        closeImageCropper();
        return;
    }

    canvas.toBlob(function(blob) {
        if (!blob) {
            closeImageCropper();
            return;
        }

        const croppedFile = new File([blob], tpCurrentFileName, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        try {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            tpActiveTargetInput.files = dataTransfer.files;
        } catch (e) {}

        const previewId = tpActiveOptions.previewId || tpActiveTargetInput.getAttribute('data-preview-id');
        if (previewId) {
            const prevEl = document.getElementById(previewId);
            if (prevEl) {
                prevEl.src = URL.createObjectURL(blob);
                prevEl.classList.remove('hidden');
            }
        } else {
            const parentForm = tpActiveTargetInput.closest('form');
            if (parentForm) {
                const nearestImg = parentForm.querySelector('img.preview-target, img[id*="preview"], img[id*="current"]');
                if (nearestImg) {
                    nearestImg.src = URL.createObjectURL(blob);
                }
            }
        }

        closeImageCropper();
    }, 'image/jpeg', 0.92);
}
</script>
