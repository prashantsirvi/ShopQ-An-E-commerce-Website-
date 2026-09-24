/**
 * ShopQ product detail page interactions.
 */
(function () {
    'use strict';

    function initGallery() {
        var gallery = document.querySelector('[data-product-gallery]');
        if (!gallery) return;

        var main = gallery.querySelector('[data-gallery-main]');
        gallery.querySelectorAll('[data-gallery-thumb]').forEach(function (button) {
            button.addEventListener('click', function () {
                gallery.querySelectorAll('.gallery-thumb').forEach(function (el) {
                    el.classList.remove('is-active');
                });
                button.classList.add('is-active');
                if (main) {
                    main.src = button.getAttribute('data-gallery-thumb') || '';
                }
            });
        });
    }

    function initVariants() {
        var picker = document.querySelector('[data-variant-picker]');
        if (!picker) return;

        picker.querySelectorAll('.variant-option').forEach(function (button) {
            button.addEventListener('click', function () {
                picker.querySelectorAll('.variant-option').forEach(function (el) {
                    el.classList.remove('is-active');
                });
                button.classList.add('is-active');

                var stock = parseInt(button.getAttribute('data-stock') || '0', 10);
                var status = document.querySelector('[data-stock-status]');
                if (status) {
                    status.innerHTML = stock > 0
                        ? '<i class="fa-solid fa-circle-check"></i> In stock (' + stock + ' available)'
                        : '<i class="fa-solid fa-circle-xmark"></i> Out of stock';
                }
            });
        });
    }

    function initQuantity() {
        var input = document.querySelector('[data-qty-input]');
        if (!input) return;

        var minus = document.querySelector('[data-qty-minus]');
        var plus = document.querySelector('[data-qty-plus]');

        if (minus) {
            minus.addEventListener('click', function () {
                input.value = String(Math.max(1, parseInt(input.value || '1', 10) - 1));
            });
        }

        if (plus) {
            plus.addEventListener('click', function () {
                var max = parseInt(input.max || '99', 10);
                input.value = String(Math.min(max, parseInt(input.value || '1', 10) + 1));
            });
        }
    }

    initGallery();
    initVariants();
    initQuantity();
})();
