// MENU MOBILE (hamburger)
document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navMobile = document.getElementById('navMobile');
    const navOverlay = document.getElementById('navOverlay');
    const navCloseBtn = document.getElementById('navCloseBtn');

    if (hamburgerBtn && navMobile && navOverlay) {
        const openMenu = () => {
            navMobile.classList.add('show');
            navOverlay.classList.add('show');
        };
        const closeMenu = () => {
            navMobile.classList.remove('show');
            navOverlay.classList.remove('show');
        };
        hamburgerBtn.addEventListener('click', openMenu);
        navOverlay.addEventListener('click', closeMenu);
        if (navCloseBtn) navCloseBtn.addEventListener('click', closeMenu);
    }

    // =========================
    // PRODUCT DETAIL: CHỌN BIẾN THỂ + AJAX
    // =========================
    const colorOptions = document.getElementById('colorOptions');
    const storageOptions = document.getElementById('storageOptions');
    const variantPriceEl = document.getElementById('variantPrice');
    const variantBasePriceEl = document.getElementById('variantBasePrice');
    const variantStockEl = document.getElementById('variantStock');
    const variantIdInput = document.getElementById('variantIdInput');

    function getActiveVariantValues() {
        let color = '';
        let storage = '';
        if (colorOptions) {
            const activeColor = colorOptions.querySelector('.variant-chip.active');
            if (activeColor) color = activeColor.dataset.color;
        }
        if (storageOptions) {
            const activeSt = storageOptions.querySelector('.variant-chip.active');
            if (activeSt) storage = activeSt.dataset.storage;
        }
        return { color, storage };
    }

    function updateVariantFromServer() {
        if (typeof productId === 'undefined') return;
        const { color, storage } = getActiveVariantValues();
        if (!color || !storage) return;

        fetch(`${window.location.origin}${window.location.pathname.includes('/pages/') ? '/../ajax/get_variant.php' : '/ajax/get_variant.php'}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams({
                product_id: productId,
                color: color,
                storage: storage
            })
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    if (variantPriceEl) variantPriceEl.textContent = 'Không có giá';
                    if (variantStockEl) variantStockEl.textContent = '0';
                    return;
                }
                const d = res.data;
                if (variantIdInput) variantIdInput.value = d.id;
                if (variantStockEl) variantStockEl.textContent = d.stock;
                if (variantBasePriceEl) variantBasePriceEl.textContent = new Intl.NumberFormat('vi-VN').format(d.price) + '₫';
                if (variantPriceEl) variantPriceEl.textContent = new Intl.NumberFormat('vi-VN').format(d.final_price) + '₫';
            })
            .catch(() => {
                if (variantPriceEl) variantPriceEl.textContent = 'Lỗi tải giá';
            });
    }

    // click chọn màu / dung lượng
    if (colorOptions) {
        colorOptions.addEventListener('click', function (e) {
            if (e.target.classList.contains('variant-chip')) {
                colorOptions.querySelectorAll('.variant-chip').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                updateVariantFromServer();
            }
        });
    }
    if (storageOptions) {
        storageOptions.addEventListener('click', function (e) {
            if (e.target.classList.contains('variant-chip')) {
                storageOptions.querySelectorAll('.variant-chip').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                updateVariantFromServer();
            }
        });
    }

    // lần đầu vào trang detail
    if (colorOptions && storageOptions) {
        updateVariantFromServer();
    }

    // =========================
    // ADD TO CART (AJAX)
    // =========================
    const addToCartForm = document.getElementById('addToCartForm');
    const addCartMsg = document.getElementById('addCartMsg');
    const cartCountEl = document.getElementById('cartCount');

    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(addToCartForm);
            const variantId = formData.get('variant_id');
            if (!variantId) {
                if (addCartMsg) addCartMsg.textContent = 'Vui lòng chọn biến thể.';
                return;
            }

            fetch(`${window.location.origin}${window.location.pathname.includes('/pages/') ? '/../ajax/cart_add.php' : '/ajax/cart_add.php'}`, {
                method: 'POST',
                body: new URLSearchParams({
                    variant_id: formData.get('variant_id'),
                    product_id: formData.get('product_id'),
                    qty: formData.get('qty') || 1
                })
            })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) {
                        if (addCartMsg) addCartMsg.textContent = res.message || 'Lỗi thêm giỏ hàng.';
                    } else {
                        if (addCartMsg) addCartMsg.textContent = 'Đã thêm vào giỏ.';
                        if (cartCountEl && res.cart_count !== undefined) cartCountEl.textContent = res.cart_count;
                    }
                })
                .catch(() => {
                    if (addCartMsg) addCartMsg.textContent = 'Lỗi mạng.';
                });
        });
    }
});
