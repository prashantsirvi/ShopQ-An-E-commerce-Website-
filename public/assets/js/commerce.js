/**
 * ShopQ commerce — cart, wishlist, compare AJAX actions and badge counts.
 */
(function () {
    'use strict';

    var cartProductIds = new Set();

    function appUrl(path) {
        var meta = document.querySelector('meta[name="app-url"]');
        var base = meta ? meta.getAttribute('content') : '';
        return base.replace(/\/$/, '') + path;
    }

    function csrfFieldName() {
        var meta = document.querySelector('meta[name="csrf-field"]');
        return meta ? meta.getAttribute('content') : '_csrf_token';
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function updateBadge(selector, count) {
        document.querySelectorAll(selector).forEach(function (el) {
            el.textContent = count > 0 ? String(count) : '';
        });
    }

    function setCartButtonState(button, inCart) {
        var icon = button.querySelector('i');
        var label = button.querySelector('span');

        button.classList.toggle('is-in-cart', inCart);
        button.classList.toggle('btn-success', inCart);
        button.classList.toggle('btn-primary', !inCart);
        button.setAttribute('aria-pressed', inCart ? 'true' : 'false');
        button.setAttribute('aria-label', inCart ? 'Remove from cart' : 'Add to cart');

        if (icon) {
            icon.className = inCart ? 'fa-solid fa-check' : 'fa-solid fa-cart-plus';
        }

        if (label) {
            label.textContent = inCart ? 'Added' : 'Add';
        }
    }

    function syncCartToggleButtons() {
        document.querySelectorAll('[data-cart-toggle]').forEach(function (button) {
            var productId = parseInt(button.getAttribute('data-cart-toggle') || '0', 10);
            setCartButtonState(button, cartProductIds.has(productId));
        });
    }

    function updateCounts(data) {
        if (typeof data.cart_count !== 'undefined') {
            updateBadge('[data-cart-count]', data.cart_count);
        }
        if (typeof data.wishlist_count !== 'undefined') {
            updateBadge('[data-wishlist-count]', data.wishlist_count);
        }
        if (typeof data.compare_count !== 'undefined') {
            updateBadge('[data-compare-count]', data.compare_count);
        }
        if (Array.isArray(data.cart_product_ids)) {
            cartProductIds = new Set(data.cart_product_ids.map(function (id) { return parseInt(id, 10); }));
            syncCartToggleButtons();
        }
    }

    function postAction(url, fields) {
        var body = new FormData();
        var tokenName = csrfFieldName();
        var token = csrfToken();

        if (tokenName && token) {
            body.append(tokenName, token);
        }

        Object.keys(fields).forEach(function (key) {
            if (fields[key] !== null && typeof fields[key] !== 'undefined') {
                body.append(key, fields[key]);
            }
        });

        return fetch(appUrl(url), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: body,
        }).then(function (response) {
            return response.text().then(function (text) {
                var data = {};

                try {
                    data = text ? JSON.parse(text) : {};
                } catch (error) {
                    throw new Error('Unexpected server response. Please refresh and try again.');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }

                return data;
            });
        });
    }

    function showToast(message, type) {
        var container = document.querySelector('.flash-container');
        if (!container) {
            alert(message);
            return;
        }

        var flash = document.createElement('div');
        flash.className = 'flash flash-' + (type || 'success');
        flash.textContent = message;
        container.appendChild(flash);

        setTimeout(function () {
            flash.remove();
        }, 3500);
    }

    function initWishlistButtons() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-wishlist-add]');
            if (!button) return;

            event.preventDefault();
            event.stopPropagation();

            var productId = button.getAttribute('data-wishlist-add');
            postAction('/wishlist/add', {
                product_id: productId,
                redirect: window.location.pathname,
            })
                .then(function (data) {
                    updateCounts(data);
                    showToast(data.message || 'Added to wishlist');
                    var icon = button.querySelector('i');
                    if (icon) {
                        icon.className = 'fa-solid fa-heart';
                    }
                })
                .catch(function (error) {
                    showToast(error.message, 'error');
                });
        });
    }

    function initCompareButtons() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-compare-add]');
            if (!button) return;

            event.preventDefault();
            event.stopPropagation();

            var productId = button.getAttribute('data-compare-add');
            postAction('/compare/add', {
                product_id: productId,
                redirect: window.location.pathname,
            })
                .then(function (data) {
                    updateCounts(data);
                    showToast(data.message || 'Added to compare');
                })
                .catch(function (error) {
                    showToast(error.message, 'error');
                });
        });
    }

    function initCartQtyPickers() {
        document.querySelectorAll('.cart-qty-form').forEach(function (form) {
            var input = form.querySelector('[data-qty-input]');
            var minus = form.querySelector('[data-qty-minus]');
            var plus = form.querySelector('[data-qty-plus]');

            if (!input) return;

            if (minus) {
                minus.addEventListener('click', function () {
                    input.value = String(Math.max(1, parseInt(input.value || '1', 10) - 1));
                });
            }

            if (plus) {
                plus.addEventListener('click', function () {
                    input.value = String(Math.min(99, parseInt(input.value || '1', 10) + 1));
                });
            }
        });
    }

    function initCartToggleButtons() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-cart-toggle]');
            if (!button) return;

            event.preventDefault();
            event.stopPropagation();

            var productId = parseInt(button.getAttribute('data-cart-toggle') || '0', 10);
            var inCart = cartProductIds.has(productId);
            var url = inCart ? '/cart/remove' : '/cart/add';
            var fields = {
                product_id: productId,
                redirect: window.location.pathname,
            };

            if (!inCart) {
                fields.quantity = 1;
            }

            button.disabled = true;

            postAction(url, fields)
                .then(function (data) {
                    updateCounts(data);
                    if (inCart) {
                        cartProductIds.delete(productId);
                    } else {
                        cartProductIds.add(productId);
                    }
                    setCartButtonState(button, !inCart);
                    showToast(data.message || (inCart ? 'Removed from cart' : 'Added to cart'));
                })
                .catch(function (error) {
                    showToast(error.message || 'Could not update cart', 'error');
                })
                .finally(function () {
                    button.disabled = false;
                });
        });
    }

    function initDetailAddToCart() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-add-to-cart]');
            if (!button || button.closest('[data-cart-toggle]')) return;

            event.preventDefault();

            var productId = button.getAttribute('data-add-to-cart');
            var detailPage = button.closest('.product-detail-page');
            var qtyInput = detailPage ? detailPage.querySelector('[data-qty-input]') : null;
            var quantity = qtyInput ? parseInt(qtyInput.value || '1', 10) : 1;
            var variantId = '';

            if (detailPage) {
                var activeVariant = detailPage.querySelector('[data-variant-picker] .variant-option.is-active');
                variantId = activeVariant ? activeVariant.getAttribute('data-variant-id') : '';
            }

            button.disabled = true;

            postAction('/cart/add', {
                product_id: productId,
                variant_id: variantId,
                quantity: quantity,
                redirect: '/cart',
            })
                .then(function (data) {
                    updateCounts(data);
                    showToast(data.message || 'Added to cart');
                })
                .catch(function (error) {
                    showToast(error.message || 'Could not add to cart', 'error');
                })
                .finally(function () {
                    button.disabled = false;
                });
        });
    }

    function loadCommerceState() {
        fetch(appUrl('/api/commerce/counts'), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (response) { return response.json(); })
            .then(updateCounts)
            .catch(function () { /* silent */ });
    }

    window.ShopQCommerce = {
        addToCart: function (productId, variantId, quantity) {
            return postAction('/cart/add', {
                product_id: productId,
                variant_id: variantId || '',
                quantity: quantity || 1,
                redirect: '/cart',
            }).then(function (data) {
                updateCounts(data);
                showToast(data.message || 'Added to cart');
                return data;
            });
        },
        updateCounts: updateCounts,
    };

    document.querySelectorAll('[data-cart-toggle]').forEach(function (button) {
        var productId = parseInt(button.getAttribute('data-cart-toggle') || '0', 10);
        if (button.classList.contains('is-in-cart')) {
            cartProductIds.add(productId);
        }
    });

    initWishlistButtons();
    initCompareButtons();
    initCartQtyPickers();
    initCartToggleButtons();
    initDetailAddToCart();
    loadCommerceState();
})();
