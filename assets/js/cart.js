document.addEventListener('DOMContentLoaded', function () {
    const cartRows = document.querySelectorAll('.cart-row');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartCountEl = document.getElementById('cartCount');

    function updateCartOnServer(variantId, action, rowEl) {
        fetch(`${window.location.origin}${window.location.pathname.includes('/pages/') ? '/../ajax/cart_update.php' : '/ajax/cart_update.php'}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams({
                variant_id: variantId,
                action: action
            })
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    alert(res.message || 'Lỗi cập nhật giỏ hàng');
                    return;
                }
                if (res.removed) {
                    if (rowEl) rowEl.remove();
                } else {
                    if (rowEl) {
                        const qtyEl = rowEl.querySelector('.cart-qty');
                        const lineTotalEl = rowEl.querySelector('.cart-line-total');
                        if (qtyEl && res.qty !== undefined) qtyEl.textContent = res.qty;
                        if (lineTotalEl && res.line_total !== undefined) {
                            lineTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(res.line_total) + '₫';
                        }
                    }
                }
                if (cartTotalEl && res.cart_total !== undefined) {
                    cartTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(res.cart_total) + '₫';
                }
                if (cartCountEl && res.cart_count !== undefined) {
                    cartCountEl.textContent = res.cart_count;
                }
            })
            .catch(() => alert('Lỗi mạng'));
    }

    cartRows.forEach(row => {
        const variantId = row.dataset.variantId;
        const btnInc = row.querySelector('.cart-inc');
        const btnDec = row.querySelector('.cart-dec');
        const btnRemove = row.querySelector('.cart-remove');

        if (btnInc) btnInc.addEventListener('click', () => updateCartOnServer(variantId, 'inc', row));
        if (btnDec) btnDec.addEventListener('click', () => updateCartOnServer(variantId, 'dec', row));
        if (btnRemove) btnRemove.addEventListener('click', () => updateCartOnServer(variantId, 'remove', row));
    });
});
