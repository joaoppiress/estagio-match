/* =========================================================================
   EstágioMatch — front-end behavior
   Vanilla JS, no dependencies, progressive enhancement.
   ========================================================================= */
(function () {
    'use strict';

    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    /* ------------------------------------------------------------------ *
     * Theme + accessibility preferences (persisted in localStorage)
     * ------------------------------------------------------------------ */
    const PREFS_KEY = 'em-prefs';
    const loadPrefs = () => {
        try { return JSON.parse(localStorage.getItem(PREFS_KEY)) || {}; }
        catch (e) { return {}; }
    };
    const savePrefs = (p) => {
        try { localStorage.setItem(PREFS_KEY, JSON.stringify(p)); } catch (e) { /* ignore */ }
    };
    let prefs = loadPrefs();

    const applyPrefs = () => {
        const root = document.documentElement;
        root.setAttribute('data-theme', prefs.theme === 'dark' ? 'dark' : 'light');
        root.setAttribute('data-contrast', prefs.contrast === 'high' ? 'high' : 'normal');
        root.style.setProperty('--font-scale', String(prefs.fontScale || 1));
        if (prefs.motion === 'reduce') {
            root.setAttribute('data-motion', 'reduce');
        } else {
            root.removeAttribute('data-motion');
        }
        // reflect state on toolbar buttons
        const themeBtn = $('[data-a11y="theme"]');
        if (themeBtn) themeBtn.setAttribute('aria-pressed', String(prefs.theme === 'dark'));
        const contrastBtn = $('[data-a11y="contrast"]');
        if (contrastBtn) contrastBtn.setAttribute('aria-pressed', String(prefs.contrast === 'high'));
        const motionBtn = $('[data-a11y="motion"]');
        if (motionBtn) motionBtn.setAttribute('aria-pressed', String(prefs.motion === 'reduce'));
    };

    // Apply early-saved theme is also handled by an inline-free bootstrap in layout head.
    applyPrefs();

    const bindA11yBar = () => {
        const clamp = (n) => Math.min(1.5, Math.max(0.8, Math.round(n * 100) / 100));

        $$('[data-a11y]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.a11y;
                if (action === 'font-up') prefs.fontScale = clamp((prefs.fontScale || 1) + 0.1);
                if (action === 'font-down') prefs.fontScale = clamp((prefs.fontScale || 1) - 0.1);
                if (action === 'font-reset') prefs.fontScale = 1;
                if (action === 'theme') prefs.theme = prefs.theme === 'dark' ? 'light' : 'dark';
                if (action === 'contrast') prefs.contrast = prefs.contrast === 'high' ? 'normal' : 'high';
                if (action === 'motion') prefs.motion = prefs.motion === 'reduce' ? 'normal' : 'reduce';
                savePrefs(prefs);
                applyPrefs();
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Mobile navigation toggle
     * ------------------------------------------------------------------ */
    const bindNavToggle = () => {
        const nav = $('.topnav');
        const toggle = $('.nav-toggle');
        if (!nav || !toggle) return;
        nav.setAttribute('data-open', 'false');
        toggle.addEventListener('click', () => {
            const open = nav.getAttribute('data-open') === 'true';
            nav.setAttribute('data-open', String(!open));
            toggle.setAttribute('aria-expanded', String(!open));
        });
    };

    /* ------------------------------------------------------------------ *
     * Toast: dismiss button + pause auto-dismiss on hover/focus
     * ------------------------------------------------------------------ */
    const bindToasts = () => {
        $$('.toast.show').forEach((toast) => {
            const DELAY = 6000;
            let remaining = DELAY;
            let start = Date.now();
            let timer = null;

            const dismiss = () => { toast.remove(); };
            const pause = () => {
                if (timer) { clearTimeout(timer); timer = null; }
                remaining -= Date.now() - start;
            };
            const resume = () => {
                start = Date.now();
                timer = setTimeout(dismiss, Math.max(800, remaining));
            };

            const close = $('.toast-close', toast);
            if (close) close.addEventListener('click', dismiss);

            toast.addEventListener('mouseenter', pause);
            toast.addEventListener('mouseleave', resume);
            toast.addEventListener('focusin', pause);
            toast.addEventListener('focusout', resume);
            resume();
        });
    };

    /* ------------------------------------------------------------------ *
     * Auth tabs — ARIA tablist with keyboard navigation
     * ------------------------------------------------------------------ */
    const bindAuthTabs = () => {
        const tabs = $$('[data-auth-tab]');
        const login = $('[data-auth-panel="login"]');
        const register = $('[data-auth-panel="cadastro"]');
        if (!tabs.length || !login || !register) return;

        const showPanel = (panel, focusTab = false) => {
            login.hidden = panel !== 'login';
            register.hidden = panel !== 'cadastro';
            tabs.forEach((tab) => {
                const active = tab.dataset.authTab === panel;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', String(active));
                tab.setAttribute('tabindex', active ? '0' : '-1');
                if (active && focusTab) tab.focus();
            });
            if (panel === 'cadastro') history.replaceState(null, '', '#cadastro');
        };

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => showPanel(tab.dataset.authTab));
            tab.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    e.preventDefault();
                    const dir = e.key === 'ArrowRight' ? 1 : -1;
                    const next = tabs[(i + dir + tabs.length) % tabs.length];
                    showPanel(next.dataset.authTab, true);
                }
            });
        });

        if (window.location.hash === '#cadastro') showPanel('cadastro');
    };

    /* ------------------------------------------------------------------ *
     * Account-type dependent fields (estudante / empresa)
     * ------------------------------------------------------------------ */
    const bindAccountType = () => {
        const accountType = $('[data-account-type]');
        if (!accountType) return;
        const companyFields = $$('[data-company-field]');
        const studentFields = $$('[data-student-field]');
        const toggle = () => {
            const isCompany = accountType.value === 'empresa';
            companyFields.forEach((el) => { el.hidden = !isCompany; });
            studentFields.forEach((el) => { el.hidden = isCompany; });
        };
        accountType.addEventListener('change', toggle);
        toggle();
    };

    /* ------------------------------------------------------------------ *
     * Clickable cards (whole-card link) — kept non-interactive for AT
     * ------------------------------------------------------------------ */
    const bindCardLinks = () => {
        $$('[data-card-link]').forEach((card) => {
            card.addEventListener('click', (event) => {
                if (event.target.closest('a,button,form,input,select,textarea,label')) return;
                window.location.href = card.dataset.cardLink;
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Password strength meter (mirrors back-end Security::strongPassword)
     * ------------------------------------------------------------------ */
    const PW_RULES = [
        { id: 'len', test: (v) => v.length >= 10, label: 'Pelo menos 10 caracteres' },
        { id: 'upper', test: (v) => /[A-Z]/.test(v), label: 'Uma letra maiúscula' },
        { id: 'lower', test: (v) => /[a-z]/.test(v), label: 'Uma letra minúscula' },
        { id: 'num', test: (v) => /\d/.test(v), label: 'Um número' },
        { id: 'sym', test: (v) => /[^A-Za-z0-9]/.test(v), label: 'Um caractere especial' }
    ];
    const PW_LEVELS = [
        { label: 'Muito fraca', color: '#dc2626' },
        { label: 'Fraca', color: '#dc2626' },
        { label: 'Média', color: '#d97706' },
        { label: 'Boa', color: '#2563eb' },
        { label: 'Forte', color: '#16a34a' }
    ];

    const bindPasswordMeter = () => {
        $$('[data-password-meter]').forEach((input) => {
            const wrap = document.createElement('div');
            wrap.className = 'pw-meter';
            wrap.dataset.level = '0';
            wrap.innerHTML =
                '<div class="pw-meter-track" aria-hidden="true">' +
                '<span class="pw-seg"></span><span class="pw-seg"></span>' +
                '<span class="pw-seg"></span><span class="pw-seg"></span></div>' +
                '<p class="pw-label" role="status" aria-live="polite"></p>' +
                '<ul class="pw-rules">' +
                PW_RULES.map((r) => `<li data-rule="${r.id}">${r.label}</li>`).join('') +
                '</ul>';
            input.insertAdjacentElement('afterend', wrap);

            const label = $('.pw-label', wrap);
            const update = () => {
                const v = input.value;
                let passed = 0;
                PW_RULES.forEach((r) => {
                    const ok = r.test(v);
                    if (ok) passed += 1;
                    const li = $(`[data-rule="${r.id}"]`, wrap);
                    if (li) li.classList.toggle('ok', ok);
                });
                const lvl = v.length === 0 ? 0 : Math.min(4, passed);
                wrap.dataset.level = String(lvl);
                const info = PW_LEVELS[lvl];
                wrap.style.setProperty('--pw-color', info.color);
                label.textContent = v.length === 0 ? '' : `Força da senha: ${info.label}`;
            };
            input.addEventListener('input', update);
            update();
        });
    };

    /* ------------------------------------------------------------------ *
     * Confirm-password matching
     * ------------------------------------------------------------------ */
    const bindConfirmPassword = () => {
        $$('[data-confirm-for]').forEach((confirm) => {
            const source = document.getElementById(confirm.dataset.confirmFor);
            if (!source) return;
            const err = confirm.parentElement.querySelector('.field-error');
            const check = () => {
                const mismatch = confirm.value !== '' && confirm.value !== source.value;
                confirm.setAttribute('aria-invalid', String(mismatch));
                if (err) {
                    err.textContent = 'As senhas não coincidem.';
                    err.classList.toggle('show', mismatch);
                }
                confirm.setCustomValidity(mismatch ? 'As senhas não coincidem.' : '');
            };
            confirm.addEventListener('input', check);
            source.addEventListener('input', check);
        });
    };

    /* ------------------------------------------------------------------ *
     * Inline field validation on blur (email / UF)
     * ------------------------------------------------------------------ */
    const bindFieldValidation = () => {
        const setError = (field, msg) => {
            const err = field.parentElement.querySelector('.field-error');
            field.setAttribute('aria-invalid', String(!!msg));
            if (err) {
                err.textContent = msg || '';
                err.classList.toggle('show', !!msg);
            }
        };

        $$('input[type="email"]').forEach((field) => {
            field.addEventListener('blur', () => {
                const v = field.value.trim();
                setError(field, v && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v) ? 'Informe um e-mail válido.' : '');
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Prevent double submit + loading state
     * ------------------------------------------------------------------ */
    const bindFormGuards = () => {
        $$('form').forEach((form) => {
            form.addEventListener('submit', (e) => {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }
                // let native validation block first
                if (!form.checkValidity()) return;
                form.dataset.submitting = 'true';
                const btn = form.querySelector('button[type="submit"], button:not([type])');
                if (btn) {
                    btn.classList.add('is-loading');
                    btn.setAttribute('aria-disabled', 'true');
                    setTimeout(() => { btn.disabled = true; }, 0);
                }
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Saved vacancies (favorites) — localStorage only
     * ------------------------------------------------------------------ */
    const FAV_KEY = 'em-saved-vacancies';
    const loadFavs = () => {
        try { return new Set(JSON.parse(localStorage.getItem(FAV_KEY)) || []); }
        catch (e) { return new Set(); }
    };
    const saveFavs = (set) => {
        try { localStorage.setItem(FAV_KEY, JSON.stringify(Array.from(set))); } catch (e) { /* ignore */ }
    };

    const bindFavorites = () => {
        const favs = loadFavs();
        $$('[data-save-vacancy]').forEach((btn) => {
            const id = btn.dataset.saveVacancy;
            const sync = () => {
                const on = favs.has(id);
                btn.setAttribute('aria-pressed', String(on));
                btn.setAttribute('aria-label', on ? 'Remover vaga salva' : 'Salvar vaga');
                btn.title = on ? 'Remover dos salvos' : 'Salvar vaga';
            };
            sync();
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (favs.has(id)) favs.delete(id); else favs.add(id);
                saveFavs(favs);
                sync();
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Client-side vacancy filtering + sorting + "load more"
     * ------------------------------------------------------------------ */
    const bindVacancyFilters = () => {
        const list = $('[data-vacancy-list]');
        if (!list) return;
        const cards = $$('[data-vacancy]', list);
        const countEl = $('[data-result-count]');
        const emptyEl = $('[data-vacancy-empty]');
        const moreBtn = $('[data-load-more]');
        const PAGE = 8;
        let shown = PAGE;

        const minScholarship = $('[data-filter="scholarship"]');
        const minScholarshipOut = $('[data-filter-out="scholarship"]');
        const modalityInputs = $$('[data-filter="modality"]');
        const savedOnly = $('[data-filter="saved"]');
        const sortSelect = $('[data-sort]');

        const matches = (card, favs) => {
            if (minScholarship) {
                const min = Number(minScholarship.value || 0);
                if (Number(card.dataset.scholarship || 0) < min) return false;
            }
            const checkedMods = modalityInputs.filter((i) => i.checked).map((i) => i.value);
            if (checkedMods.length && !checkedMods.includes(card.dataset.modality)) return false;
            if (savedOnly && savedOnly.checked && !favs.has(card.dataset.vacancy)) return false;
            return true;
        };

        const apply = () => {
            const favs = loadFavs(); // re-read so "saved only" reflects live toggles
            if (minScholarshipOut && minScholarship) {
                minScholarshipOut.textContent = 'R$ ' + Number(minScholarship.value || 0).toLocaleString('pt-BR');
            }
            let visible = cards.filter((c) => matches(c, favs));

            if (sortSelect) {
                const mode = sortSelect.value;
                visible.sort((a, b) => {
                    if (mode === 'scholarship') return Number(b.dataset.scholarship) - Number(a.dataset.scholarship);
                    if (mode === 'recent') return Number(a.dataset.order) - Number(b.dataset.order);
                    return Number(b.dataset.match) - Number(a.dataset.match); // default: match
                });
                visible.forEach((c) => list.appendChild(c));
            }

            cards.forEach((c) => { c.hidden = true; });
            visible.slice(0, shown).forEach((c) => { c.hidden = false; });

            if (countEl) countEl.textContent = String(visible.length);
            if (emptyEl) emptyEl.hidden = visible.length !== 0;
            if (moreBtn) moreBtn.hidden = visible.length <= shown;
        };

        [minScholarship, savedOnly, sortSelect].forEach((el) => {
            if (el) el.addEventListener('input', () => { shown = PAGE; apply(); });
        });
        modalityInputs.forEach((el) => el.addEventListener('change', () => { shown = PAGE; apply(); }));
        if (moreBtn) moreBtn.addEventListener('click', () => { shown += PAGE; apply(); });

        apply();
    };

    /* ------------------------------------------------------------------ *
     * Animate progress bars / match fills on load (respects reduced-motion)
     * ------------------------------------------------------------------ */
    const animateBars = () => {
        $$('[data-fill]').forEach((bar) => {
            const target = bar.dataset.fill + '%';
            requestAnimationFrame(() => { bar.style.width = target; });
        });
    };

    /* ------------------------------------------------------------------ *
     * Scroll reveal — adds .in when element enters the viewport
     * ------------------------------------------------------------------ */
    const bindReveal = () => {
        const items = $$('[data-reveal]');
        if (!items.length) return;
        if (!('IntersectionObserver' in window)) {
            items.forEach((el) => el.classList.add('in'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
        items.forEach((el) => io.observe(el));
    };

    /* ------------------------------------------------------------------ *
     * Animated counters — counts up to data-counter when first seen
     * ------------------------------------------------------------------ */
    const runCounter = (el) => {
        const target = parseFloat(el.dataset.counter);
        const decimals = (el.dataset.counter.split('.')[1] || '').length;
        const suffix = el.dataset.suffix || '';
        const prefix = el.dataset.prefix || '';
        const dur = 1400;
        const start = performance.now();
        const reduced = document.documentElement.dataset.motion === 'reduce'
            || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduced) {
            el.textContent = prefix + target.toLocaleString('pt-BR') + suffix;
            return;
        }
        const tick = (now) => {
            const p = Math.min(1, (now - start) / dur);
            const eased = 1 - Math.pow(1 - p, 3);
            const val = target * eased;
            el.textContent = prefix + val.toLocaleString('pt-BR', {
                minimumFractionDigits: decimals, maximumFractionDigits: decimals
            }) + suffix;
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    const bindCounters = () => {
        const els = $$('[data-counter]');
        if (!els.length) return;
        if (!('IntersectionObserver' in window)) { els.forEach(runCounter); return; }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) { runCounter(entry.target); io.unobserve(entry.target); }
            });
        }, { threshold: 0.6 });
        els.forEach((el) => io.observe(el));
    };

    /* ------------------------------------------------------------------ *
     * Carousel engine — drag/scroll-snap + prev/next + dots + autoplay
     * ------------------------------------------------------------------ */
    const bindCarousels = () => {
        $$('[data-carousel]').forEach((root) => {
            const viewport = $('.carousel-viewport', root);
            const track = $('.carousel-track', root);
            const prev = $('[data-carousel-prev]', root);
            const next = $('[data-carousel-next]', root);
            const dotsWrap = $('[data-carousel-dots]', root);
            if (!viewport || !track) return;
            const slides = $$('.carousel-slide', track);

            const slideStep = () => {
                const first = slides[0];
                if (!first) return viewport.clientWidth;
                const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0) || 18;
                return first.getBoundingClientRect().width + gap;
            };

            if (prev) prev.addEventListener('click', () => viewport.scrollBy({ left: -slideStep(), behavior: 'smooth' }));
            if (next) next.addEventListener('click', () => viewport.scrollBy({ left: slideStep(), behavior: 'smooth' }));

            // Dots
            let dots = [];
            if (dotsWrap) {
                slides.forEach((_, i) => {
                    const d = document.createElement('button');
                    d.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                    d.type = 'button';
                    d.setAttribute('aria-label', 'Ir para o slide ' + (i + 1));
                    d.addEventListener('click', () => viewport.scrollTo({ left: i * slideStep(), behavior: 'smooth' }));
                    dotsWrap.appendChild(d);
                    dots.push(d);
                });
            }

            const syncDots = () => {
                const idx = Math.round(viewport.scrollLeft / slideStep());
                dots.forEach((d, i) => d.classList.toggle('active', i === idx));
            };
            viewport.addEventListener('scroll', () => { window.requestAnimationFrame(syncDots); }, { passive: true });

            // Autoplay (pauses on hover/focus/touch), respects reduced motion
            const reduced = document.documentElement.dataset.motion === 'reduce'
                || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!reduced && root.dataset.autoplay !== 'off' && slides.length > 1) {
                let timer = null;
                const advance = () => {
                    const atEnd = viewport.scrollLeft + viewport.clientWidth >= track.scrollWidth - 8;
                    if (atEnd) viewport.scrollTo({ left: 0, behavior: 'smooth' });
                    else viewport.scrollBy({ left: slideStep(), behavior: 'smooth' });
                };
                const play = () => { timer = setInterval(advance, 4500); };
                const stop = () => { if (timer) clearInterval(timer); timer = null; };
                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', play);
                root.addEventListener('focusin', stop);
                root.addEventListener('focusout', play);
                play();
            }
        });
    };

    /* ------------------------------------------------------------------ *
     * Spotlight — feature cards track the cursor for a soft glow
     * ------------------------------------------------------------------ */
    const bindSpotlight = () => {
        $$('.feature-card').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
                card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
            });
        });
    };

    /* ------------------------------------------------------------------ *
     * Init
     * ------------------------------------------------------------------ */
    document.addEventListener('DOMContentLoaded', () => {
        bindA11yBar();
        bindNavToggle();
        bindToasts();
        bindAuthTabs();
        bindAccountType();
        bindCardLinks();
        bindPasswordMeter();
        bindConfirmPassword();
        bindFieldValidation();
        bindFormGuards();
        bindFavorites();
        bindVacancyFilters();
        animateBars();
        bindReveal();
        bindCounters();
        bindCarousels();
        bindSpotlight();
    });

    /* ------------------------------------------------------------------ *
     * Service worker (PWA) — registered if supported
     * ------------------------------------------------------------------ */
    if ('serviceWorker' in navigator && document.documentElement.dataset.sw) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register(document.documentElement.dataset.sw).catch(() => { /* ignore */ });
        });
    }
})();
