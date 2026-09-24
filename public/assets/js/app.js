/**
 * ShopQ Frontend — theme, mobile nav, lazy images, live search, flash timer.
 */
(function () {
    'use strict';

    var THEME_KEY = 'shopq_theme';

    function appUrl(path) {
        var meta = document.querySelector('meta[name="app-url"]');
        var base = meta ? meta.getAttribute('content') : '';
        return base.replace(/\/$/, '') + path;
    }

    function initThemeToggle() {
        var toggle = document.querySelector('[data-theme-toggle]');
        if (!toggle) return;

        var root = document.documentElement;
        var icon = toggle.querySelector('i');

        function syncIcon() {
            var isDark = root.getAttribute('data-theme') === 'dark';
            if (icon) {
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        syncIcon();

        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem(THEME_KEY, next);
            syncIcon();
        });
    }

    function initMobileNav() {
        var drawer = document.querySelector('[data-mobile-drawer]');
        var openBtn = document.querySelector('[data-mobile-toggle]');
        if (!drawer || !openBtn) return;

        function openDrawer() {
            drawer.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            drawer.hidden = true;
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openDrawer);
        drawer.querySelectorAll('[data-mobile-close]').forEach(function (el) {
            el.addEventListener('click', closeDrawer);
        });
    }

    function initLazyImages() {
        document.querySelectorAll('.lazy-image').forEach(function (img) {
            function markLoaded() {
                img.classList.add('is-loaded');
            }

            if (img.complete) {
                markLoaded();
            } else {
                img.addEventListener('load', markLoaded, { once: true });
                img.addEventListener('error', markLoaded, { once: true });
            }
        });
    }

    function initSearchSuggestions() {
        var input = document.querySelector('[data-search-input]');
        var panel = document.querySelector('[data-search-suggestions]');
        if (!input || !panel) return;

        var debounceTimer;
        var defaultHtml = panel.innerHTML;

        input.addEventListener('focus', function () {
            panel.hidden = false;
        });

        document.addEventListener('click', function (event) {
            if (!panel.contains(event.target) && event.target !== input) {
                panel.hidden = true;
            }
        });

        panel.querySelectorAll('[data-suggestion]').forEach(function (button) {
            button.addEventListener('click', function () {
                input.value = button.getAttribute('data-suggestion') || '';
                input.form && input.form.submit();
            });
        });

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var query = input.value.trim();

            if (query.length < 2) {
                panel.innerHTML = defaultHtml;
                panel.hidden = false;
                bindPopularSuggestions();
                return;
            }

            debounceTimer = setTimeout(function () {
                fetch(appUrl('/api/search/suggestions?q=' + encodeURIComponent(query)), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (!data.items || !data.items.length) {
                            panel.innerHTML = '<p class="search-empty">No matching products</p>';
                            panel.hidden = false;
                            return;
                        }

                        panel.innerHTML = data.items.map(function (item) {
                            return '<a class="search-result" href="' + item.url + '">' +
                                '<img src="' + item.image + '" alt="">' +
                                '<span><strong>' + item.name + '</strong><small>' + item.price + '</small></span>' +
                                '</a>';
                        }).join('');
                        panel.hidden = false;
                    })
                    .catch(function () { /* silent */ });
            }, 250);
        });

        function bindPopularSuggestions() {
            panel.querySelectorAll('[data-suggestion]').forEach(function (button) {
                button.addEventListener('click', function () {
                    input.value = button.getAttribute('data-suggestion') || '';
                    input.form && input.form.submit();
                });
            });
        }
    }

    function initFlashTimer() {
        var timer = document.querySelector('[data-flash-timer]');
        var display = document.querySelector('[data-timer-display]');
        if (!timer || !display) return;

        var end = new Date(timer.getAttribute('data-end') || '').getTime();
        if (!end) return;

        function pad(value) {
            return String(value).padStart(2, '0');
        }

        function tick() {
            var diff = Math.max(0, end - Date.now());
            var days = Math.floor(diff / 86400000);
            var hours = Math.floor((diff % 86400000) / 3600000);
            var minutes = Math.floor((diff % 3600000) / 60000);
            var seconds = Math.floor((diff % 60000) / 1000);
            display.textContent = pad(days) + ':' + pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
        }

        tick();
        setInterval(tick, 1000);
    }

    initThemeToggle();
    initMobileNav();
    initLazyImages();
    initSearchSuggestions();
    initFlashTimer();
})();
