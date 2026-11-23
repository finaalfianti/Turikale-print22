(function () {
    'use strict';

    const MobileMenu = {
        init() {
            const toggleBtn  = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon   = document.getElementById('menu-icon');

            if (!toggleBtn || !mobileMenu || !menuIcon) return;

            let isOpen = false;

            toggleBtn.addEventListener('click', () => {
                isOpen = !isOpen;

                if (isOpen) {
                    mobileMenu.classList.remove('scale-y-0', 'opacity-0');
                    mobileMenu.classList.add('scale-y-100', 'opacity-100');
                    mobileMenu.parentElement.removeAttribute('aria-hidden');
                    menuIcon.textContent = 'close';
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.setAttribute('aria-label', 'Tutup menu navigasi');
                } else {
                    mobileMenu.classList.remove('scale-y-100', 'opacity-100');
                    mobileMenu.classList.add('scale-y-0', 'opacity-0');
                    mobileMenu.parentElement.setAttribute('aria-hidden', 'true');
                    menuIcon.textContent = 'menu';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.setAttribute('aria-label', 'Buka menu navigasi');
                }
            });

            document.addEventListener('click', (e) => {
                if (isOpen && !toggleBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                    isOpen = false;
                    mobileMenu.classList.remove('scale-y-100', 'opacity-100');
                    mobileMenu.classList.add('scale-y-0', 'opacity-0');
                    mobileMenu.parentElement.setAttribute('aria-hidden', 'true');
                    menuIcon.textContent = 'menu';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.setAttribute('aria-label', 'Buka menu navigasi');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isOpen) {
                    isOpen = false;
                    mobileMenu.classList.remove('scale-y-100', 'opacity-100');
                    mobileMenu.classList.add('scale-y-0', 'opacity-0');
                    mobileMenu.parentElement.setAttribute('aria-hidden', 'true');
                    menuIcon.textContent = 'menu';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.setAttribute('aria-label', 'Buka menu navigasi');
                    toggleBtn.focus();
                }
            });
        }
    };

    const HeroSlider = {
        init() {
            const slides  = document.querySelectorAll('.hero-slide');
            const nextBtn = document.getElementById('nextSlide');
            const prevBtn = document.getElementById('prevSlide');

            if (!slides.length) return;

            let index    = 0;
            let autoTimer = null;

            const showSlide = (i) => {
                slides.forEach((slide, idx) => {
                    const isActive = idx === i;
                    slide.classList.toggle('opacity-100', isActive);
                    slide.classList.toggle('opacity-0', !isActive);
                    slide.classList.toggle('pointer-events-none', !isActive);
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                });
            };

            const nextSlide = () => {
                index = (index + 1) % slides.length;
                showSlide(index);
            };

            const prevSlide = () => {
                index = (index - 1 + slides.length) % slides.length;
                showSlide(index);
            };

            const startAuto = () => {
                autoTimer = setInterval(nextSlide, 8000);
            };

            const resetAuto = () => {
                clearInterval(autoTimer);
                startAuto();
            };

            if (nextBtn) {
                nextBtn.addEventListener('click', () => { nextSlide(); resetAuto(); });
            }
            if (prevBtn) {
                prevBtn.addEventListener('click', () => { prevSlide(); resetAuto(); });
            }

            let touchStartX = 0;
            const sliderEl = document.getElementById('hero-slider');
            if (sliderEl) {
                sliderEl.addEventListener('touchstart', (e) => {
                    touchStartX = e.touches[0].clientX;
                }, { passive: true });
                sliderEl.addEventListener('touchend', (e) => {
                    const dx = e.changedTouches[0].clientX - touchStartX;
                    if (Math.abs(dx) > 50) {
                        dx < 0 ? nextSlide() : prevSlide();
                        resetAuto();
                    }
                }, { passive: true });
            }

            const heroSection = document.getElementById('hero');
            if (heroSection) {
                heroSection.addEventListener('mouseenter', () => clearInterval(autoTimer));
                heroSection.addEventListener('mouseleave', startAuto);
                heroSection.addEventListener('focusin',   () => clearInterval(autoTimer));
                heroSection.addEventListener('focusout',  startAuto);
            }

            showSlide(0);
            startAuto();
        }
    };

    const Calculator = {
        get HARGA_PER_METER() {
            return (window.DYNAMIC_PRICES && window.DYNAMIC_PRICES.spanduk) ? window.DYNAMIC_PRICES.spanduk : 25000;
        },

        animateCountUp(el, target, duration) {
            const step      = 16;
            const steps     = Math.ceil(duration / step);
            const increment = target / steps;
            let   current   = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = 'Rp\u00A0' + Math.round(current).toLocaleString('id-ID');
            }, step);
        },

        calculateArea(p, l) {
            const smallestCm = Math.min(p, l);
            const largestMeter = Math.max(p, l) / 100;

            const rules = window.CALC_RULES || [];
            let lebarBahanCm = null;

            for (let i = 0; i < rules.length; i++) {
                if (smallestCm >= rules[i].min && smallestCm <= rules[i].max) {
                    lebarBahanCm = rules[i].rounded;
                    break;
                }
            }

            if (lebarBahanCm === null) {
                lebarBahanCm = Math.ceil(smallestCm / 100) * 100;
            }

            const lebarBahanMeter = lebarBahanCm / 100;
            let luas = lebarBahanMeter * largestMeter;

            if (luas < 1) {
                if      (luas <= 0.6) luas = 0.6;
                else if (luas <= 0.8) luas = 0.8;
                else                  luas = 1.0;
            }

            return luas;
        },

        init() {
            const section   = document.getElementById('calc-section');
            const calcBtn   = document.getElementById('hitungHargaBtn');
            const calcResult = document.getElementById('hasilHarga');

            if (!section || !calcBtn || !calcResult) return;

            const obs = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    section.classList.add('visible');
                    obs.disconnect();
                }
            }, { threshold: 0.15 });
            obs.observe(section);

            calcBtn.addEventListener('click', (e) => {
                const rect   = calcBtn.getBoundingClientRect();
                const ripple = document.createElement('span');
                ripple.className = 'btn-ripple';
                ripple.style.left = (e.clientX - rect.left - 30) + 'px';
                ripple.style.top  = (e.clientY - rect.top  - 30) + 'px';
                calcBtn.appendChild(ripple);
                ripple.addEventListener('animationend', () => ripple.remove());
            });

            calcBtn.addEventListener('click', () => {
                const pInput = document.getElementById('panjang');
                const lInput = document.getElementById('lebar');
                const p = Number(pInput?.value);
                const l = Number(lInput?.value);

                if (!p || !l || p <= 0 || l <= 0) {
                    calcResult.classList.remove('calc-shake');
                    calcResult.innerHTML = '<p role="alert" class="text-red-500 font-semibold">⚠️ Masukkan panjang dan lebar yang valid!</p>';
                    calcResult.style.display = 'block';
                    void calcResult.offsetWidth;
                    calcResult.classList.add('calc-shake');
                    return;
                }

                const luas  = this.calculateArea(p, l);
                const total = Math.ceil((luas * this.HARGA_PER_METER) / 1000) * 1000;

                calcResult.classList.remove('calc-pop', 'calc-shake');
                calcResult.innerHTML = `
<div style="
    text-align:center;
    background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(19,109,236,.08));
    padding:18px 28px;
    border-radius:14px;
    display:inline-block;
    margin-top:10px;
    border:1.5px solid rgba(16,185,129,.25);
    box-shadow:0 4px 24px rgba(16,185,129,.1);
">
    <p style="font-size:12px;color:#64748b;margin-bottom:4px;letter-spacing:.05em;text-transform:uppercase;">Estimasi Harga</p>
    <span id="resultPrice" style="
        font-size:32px;
        font-weight:800;
        background:linear-gradient(90deg,#10b981,#136dec);
        -webkit-background-clip:text;
        -webkit-text-fill-color:transparent;
        background-clip:text;
    " aria-live="polite">Rp&nbsp;0</span>
    <div style="font-size:11px;color:#64748b;margin-top:8px;padding-top:8px;border-top:1px solid rgba(16,185,129,.2);">
        Luas terhitung: <b>${luas.toFixed(2)} m²</b>
    </div>
</div>`;

                calcResult.style.display = 'block';
                void calcResult.offsetWidth;
                calcResult.classList.add('calc-pop');

                const priceEl = document.getElementById('resultPrice');
                this.animateCountUp(priceEl, total, 700);
            });
        }
    };

    const FeatureSection = {
        init() {
            const section = document.getElementById('feature-section');
            if (!section) return;

            const title = document.getElementById('feature-title');
            const sub   = document.getElementById('feature-sub');
            const cards = document.querySelectorAll('.feat-card');

            const obs = new IntersectionObserver((entries) => {
                if (!entries[0].isIntersecting) return;

                if (title) title.classList.add('type-start');
                if (sub)   setTimeout(() => sub.classList.add('fade-in'), 100);

                cards.forEach((card, i) => {
                    setTimeout(() => card.classList.add('card-visible'), 1500 + i * 180);
                });

                obs.disconnect();
            }, { threshold: 0.2 });

            obs.observe(section);
        }
    };

    const ProductSection = {
        init() {
            const section   = document.getElementById('produk-section');
            if (!section) return;

            const titleEl   = document.getElementById('produk-title');
            const subEl     = document.getElementById('produk-sub');
            const prodCards = document.querySelectorAll('.prod-card');

            const obs = new IntersectionObserver((entries) => {
                if (!entries[0].isIntersecting) return;
                if (titleEl) titleEl.classList.add('type-start');
                if (subEl)   setTimeout(() => subEl.classList.add('fade-in'), 100);
                prodCards.forEach((c, i) => {
                    setTimeout(() => c.classList.add('card-visible'), 1200 + i * 160);
                });
                obs.disconnect();
            }, { threshold: 0.15 });
            obs.observe(section);

            const wrap   = document.getElementById('produk-track-wrap');
            const track  = document.getElementById('produk-track');
            const dotsEl = document.getElementById('produk-dots');

            if (!wrap || !track || !dotsEl) return;

            const slides = track.querySelectorAll('.prod-slide');
            if (!slides.length) return;

            let current = 0;

            slides.forEach((_, i) => {
                const d = document.createElement('button');
                d.className = 'prod-dot' + (i === 0 ? ' active' : '');
                d.setAttribute('aria-label', 'Produk ' + (i + 1));
                d.addEventListener('click', () => goTo(i));
                dotsEl.appendChild(d);
            });

            const getDots = () => dotsEl.querySelectorAll('.prod-dot');

            const goTo = (idx) => {
                current = Math.max(0, Math.min(idx, slides.length - 1));
                const slideW = slides[0].offsetWidth + 16;
                track.style.transform = `translateX(-${current * slideW}px)`;
                getDots().forEach((d, i) => d.classList.toggle('active', i === current));
            };

            let startX = 0, startT = 0, moved = false;
            wrap.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startT = Date.now();
                moved  = false;
            }, { passive: true });
            wrap.addEventListener('touchmove', () => { moved = true; }, { passive: true });
            wrap.addEventListener('touchend', (e) => {
                if (!moved) return;
                const dx = e.changedTouches[0].clientX - startX;
                const dt = Date.now() - startT;
                if (Math.abs(dx) > 40 || (Math.abs(dx) > 20 && dt < 300)) {
                    goTo(current + (dx < 0 ? 1 : -1));
                }
            });

            let dragging = false, dragStartX = 0;
            wrap.addEventListener('mousedown', (e) => {
                dragging   = true;
                dragStartX = e.clientX;
                wrap.classList.add('dragging');
            });
            window.addEventListener('mousemove', (e) => {
                if (!dragging) return;
                e.preventDefault();
            });
            window.addEventListener('mouseup', (e) => {
                if (!dragging) return;
                dragging = false;
                wrap.classList.remove('dragging');
                const dx = e.clientX - dragStartX;
                if (Math.abs(dx) > 40) goTo(current + (dx < 0 ? 1 : -1));
            });
        }
    };

    const Timeline = {
        init() {
            const track = document.getElementById('tl-track');
            const fill  = document.getElementById('tl-fill');
            const items = Array.from(document.querySelectorAll('[data-tl-item]'));

            if (!track || !fill || !items.length) return;

            const getProgress = () => {
                const rect   = track.getBoundingClientRect();
                const winH   = window.innerHeight;
                const start  = rect.top    - winH * 0.85;
                const end    = rect.bottom - winH * 0.15;
                const passed = -start;
                return Math.max(0, Math.min(1, passed / (end - start)));
            };

            const onScroll = () => {
                fill.style.height = (getProgress() * 100) + '%';
                items.forEach((item) => {
                    const r   = item.getBoundingClientRect();
                    const mid = r.top + r.height / 2;
                    if (mid < window.innerHeight * 0.82) {
                        item.classList.add('active');
                    }
                });
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }
    };

    const FilterPanel = {
        init() {
            const toggleBtn  = document.getElementById('filter-toggle');
            const filterPanel = document.getElementById('filter-panel');
            const filterArrow = document.getElementById('filter-arrow');

            if (!toggleBtn || !filterPanel) return;

            let filterOpen = false;

            toggleBtn.addEventListener('click', () => {
                filterOpen = !filterOpen;
                if (filterOpen) {
                    filterPanel.classList.remove('hidden');
                    if (filterArrow) filterArrow.style.transform = 'rotate(180deg)';
                    toggleBtn.setAttribute('aria-expanded', 'true');
                } else {
                    filterPanel.classList.add('hidden');
                    if (filterArrow) filterArrow.style.transform = 'rotate(0deg)';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    };

    const LocationFilter = {
        init() {
            const provinceEl   = document.getElementById('province');
            const cityEl       = document.getElementById('city');
            const selectedCity = (typeof SELECTED_CITY !== 'undefined') ? SELECTED_CITY : '';

            if (!provinceEl || !cityEl) return;

            const loadCities = (provinceId, preselectId = '') => {
                if (!provinceId) {
                    cityEl.innerHTML = '<option value="">Pilih Kabupaten</option>';
                    return;
                }
                const primaryUrl = '../api/get-cities.php?province_id=' + provinceId;
                const fallbackUrl = '../nam1tp/api/get-cities.php?province_id=' + provinceId;

                fetch(primaryUrl)
                    .then(res => res.ok ? res.json() : fetch(fallbackUrl).then(r => r.json()))
                    .then(data => {
                        let html = '<option value="">Pilih Kabupaten/Kota</option>';
                        if (Array.isArray(data)) {
                            data.forEach(c => {
                                const sel = c.id == preselectId ? ' selected' : '';
                                html += `<option value="${c.id}"${sel}>${c.name}</option>`;
                            });
                        }
                        cityEl.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Gagal memuat kabupaten/kota:', err);
                    });
            };

            provinceEl.addEventListener('change', () => loadCities(provinceEl.value));

            if (provinceEl.value) {
                loadCities(provinceEl.value, selectedCity);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        MobileMenu.init();
        HeroSlider.init();
        Calculator.init();
        FeatureSection.init();
        ProductSection.init();
        Timeline.init();
        FilterPanel.init();
        LocationFilter.init();
    });

})();
