document.addEventListener("DOMContentLoaded", function () {
    var cards = document.querySelectorAll(".store-card");
    const cart = [];

    const drawer = document.getElementById('storeCartDrawer');
    const overlay = document.getElementById('storeOverlay');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalNode = document.getElementById('cartTotal');
    const cartCountNode = document.getElementById('cartCount');
    const checkoutModal = document.getElementById('checkoutModal');
    const checkoutForm = document.getElementById('checkoutForm');

    cards.forEach(function (card, index) {
        card.style.opacity = "0";
        card.style.transform = "translateY(10px)";

        setTimeout(function () {
            card.style.transition = "opacity 260ms ease, transform 260ms ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, index * 60);

        const addBtn = card.querySelector('.store-add-btn');
        const qtyInput = card.querySelector('.store-qty-input');
        const variantSelect = card.querySelector('.store-variant-select');

        if (!addBtn || !qtyInput || !variantSelect) {
            return;
        }

        addBtn.addEventListener('click', () => {
            const variantId = Number(variantSelect.value || 0);
            const qty = Math.max(1, Number(qtyInput.value || 1));
            const option = variantSelect.options[variantSelect.selectedIndex];
            if (!variantId || !option) {
                alert('Please select a valid size option.');
                return;
            }

            const productId = Number(card.dataset.productId || 0);
            const productName = String(card.dataset.productName || 'Item');
            const size = String(option.dataset.size || 'One Size');
            const price = Number(option.dataset.price || 0);
            if (price <= 0) {
                alert('Invalid product price.');
                return;
            }

            const existing = cart.find((item) => item.variant_id === variantId);
            if (existing) {
                existing.quantity += qty;
            } else {
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    variant_id: variantId,
                    size: size,
                    price: price,
                    quantity: qty,
                });
            }

            qtyInput.value = '1';
            renderCart();
            openCart();
        });
    });

    function openCart() {
        if (!drawer || !overlay) {
            return;
        }
        drawer.classList.add('open');
        overlay.hidden = false;
    }

    function closeCart() {
        if (!drawer || !overlay) {
            return;
        }
        drawer.classList.remove('open');
        overlay.hidden = true;
    }

    function openCheckoutModal() {
        if (!checkoutModal || !overlay) {
            return;
        }
        if (!cart.length) {
            alert('Your cart is empty.');
            return;
        }
        checkoutModal.hidden = false;
        overlay.hidden = false;
    }

    function closeCheckoutModal() {
        if (!checkoutModal) {
            return;
        }
        checkoutModal.hidden = true;
    }

    function renderCart() {
        if (!cartItemsContainer || !cartTotalNode || !cartCountNode) {
            return;
        }

        if (!cart.length) {
            cartItemsContainer.innerHTML = '<p style="color:#6b7280;">Cart is empty.</p>';
            cartTotalNode.textContent = '0.00';
            cartCountNode.textContent = '0';
            return;
        }

        let total = 0;
        cartItemsContainer.innerHTML = cart.map((item, idx) => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;
            return `<div class="store-cart-item">
                <div>
                    <strong>${item.product_name}</strong>
                    <div>Size: ${item.size}</div>
                    <div>Rs. ${item.price.toFixed(2)} x ${item.quantity}</div>
                </div>
                <button type="button" data-remove-index="${idx}">Remove</button>
            </div>`;
        }).join('');

        cartTotalNode.textContent = total.toFixed(2);
        cartCountNode.textContent = String(cart.reduce((sum, item) => sum + item.quantity, 0));

        cartItemsContainer.querySelectorAll('button[data-remove-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.removeIndex || -1);
                if (idx >= 0) {
                    cart.splice(idx, 1);
                    renderCart();
                }
            });
        });
    }

    async function submitCheckout(event) {
        event.preventDefault();
        if (!checkoutForm) {
            return;
        }

        const formData = new FormData(checkoutForm);
        const payload = {
            buyer_name: String(formData.get('buyer_name') || '').trim(),
            buyer_phone: String(formData.get('buyer_phone') || '').trim(),
            buyer_email: String(formData.get('buyer_email') || '').trim(),
            buyer_address: String(formData.get('buyer_address') || '').trim(),
            items: cart.map((item) => ({
                variant_id: item.variant_id,
                quantity: item.quantity,
            })),
        };

        try {
            const response = await fetch((window.STORE_CONFIG && window.STORE_CONFIG.checkoutUrl) || '/store/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (!response.ok || !result || !result.success) {
                throw new Error((result && result.message) ? result.message : 'Checkout failed');
            }

            alert(`Order placed successfully. Order Number: ${result.order.order_number}`);
            cart.splice(0, cart.length);
            checkoutForm.reset();
            closeCheckoutModal();
            closeCart();
            renderCart();
            window.location.reload();
        } catch (error) {
            alert(error.message || 'Failed to place order.');
        }
    }

    document.getElementById('openCartBtn')?.addEventListener('click', openCart);
    document.getElementById('closeCartBtn')?.addEventListener('click', closeCart);
    document.getElementById('openCheckoutBtn')?.addEventListener('click', openCheckoutModal);
    document.getElementById('cancelCheckoutBtn')?.addEventListener('click', closeCheckoutModal);
    overlay?.addEventListener('click', () => {
        closeCheckoutModal();
        closeCart();
    });
    checkoutForm?.addEventListener('submit', submitCheckout);

    renderCart();
});
