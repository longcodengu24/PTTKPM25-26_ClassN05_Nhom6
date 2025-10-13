/**
 * Cart Purchase Validation - Ngăn thêm sản phẩm đã mua vào giỏ hàng
 */

class CartPurchaseValidator {
    constructor() {
        this.purchasedProducts = new Set();
        this.loadPurchasedProducts();
    }

    /**
     * Load danh sách sản phẩm đã mua từ server
     */
    async loadPurchasedProducts() {
        try {
            const response = await fetch('/api/cart/purchased-products', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const data = await response.json();

            if (data.success && data.purchased_products) {
                this.purchasedProducts = new Set(data.purchased_products);
                console.log('✅ Loaded purchased products:', this.purchasedProducts.size);
            }
        } catch (error) {
            console.error('❌ Error loading purchased products:', error);
        }
    }

    /**
     * Kiểm tra xem có thể thêm sản phẩm vào giỏ hàng không
     */
    async canAddToCart(productId) {
        // Kiểm tra cache trước
        if (this.purchasedProducts.has(productId)) {
            return {
                success: false,
                can_add: false,
                message: 'Bạn đã mua sản phẩm này rồi'
            };
        }

        try {
            const response = await fetch('/api/cart/can-add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();

            // Update cache nếu đã mua
            if (!data.can_add) {
                this.purchasedProducts.add(productId);
            }

            return data;
        } catch (error) {
            console.error('❌ Error checking cart eligibility:', error);
            return {
                success: false,
                can_add: false,
                message: 'Lỗi kiểm tra giỏ hàng'
            };
        }
    }

    /**
     * Validate toàn bộ giỏ hàng
     */
    async validateCart(cartItems) {
        try {
            const response = await fetch('/api/cart/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ cart_items: cartItems })
            });

            return await response.json();
        } catch (error) {
            console.error('❌ Error validating cart:', error);
            return {
                success: false,
                valid: false,
                message: 'Lỗi validate giỏ hàng'
            };
        }
    }

    /**
     * Hiển thị thông báo cho sản phẩm đã mua
     */
    showPurchasedProductMessage(productName) {
        // Tạo hoặc update thông báo
        const existingAlert = document.querySelector('.purchased-product-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = 'alert alert-info purchased-product-alert';
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        `;

        alert.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <div>
                    <strong>Đã sở hữu</strong><br>
                    <small>Bạn đã mua "${productName}" rồi</small>
                </div>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(alert);

        // Auto remove sau 5 giây
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    }

    /**
     * Disable add to cart button cho sản phẩm đã mua
     */
    disableAddToCartButton(productId, productName) {
        const buttons = document.querySelectorAll(`[data-product-id="${productId}"]`);

        buttons.forEach(button => {
            if (button.classList.contains('add-to-cart-btn') ||
                button.textContent.includes('Thêm vào giỏ') ||
                button.textContent.includes('Add to Cart')) {

                button.disabled = true;
                button.classList.add('btn-secondary');
                button.classList.remove('btn-primary', 'btn-success');
                button.innerHTML = '<i class="fas fa-check me-1"></i> Đã mua';

                // Thêm tooltip
                button.setAttribute('title', 'Bạn đã mua sản phẩm này rồi');
                button.setAttribute('data-bs-toggle', 'tooltip');
            }
        });
    }

    /**
     * Khởi tạo validation cho trang shop
     */
    initializeShopPage() {
        // Disable các nút add to cart cho sản phẩm đã mua
        this.purchasedProducts.forEach(productId => {
            this.disableAddToCartButton(productId);
        });

        // Intercept add to cart clicks
        document.addEventListener('click', async (e) => {
            const addToCartBtn = e.target.closest('.add-to-cart-btn, [data-action="add-to-cart"]');
            if (!addToCartBtn) return;

            const productId = addToCartBtn.getAttribute('data-product-id');
            const productName = addToCartBtn.getAttribute('data-product-name') || 'sản phẩm này';

            if (!productId) return;

            e.preventDefault();
            e.stopPropagation();

            // Kiểm tra quyền thêm vào giỏ
            const validation = await this.canAddToCart(productId);

            if (!validation.can_add) {
                this.showPurchasedProductMessage(productName);
                this.disableAddToCartButton(productId, productName);
                return false;
            }

            // Cho phép thêm vào giỏ hàng
            return true;
        });
    }

    /**
     * Khởi tạo validation cho trang checkout
     */
    async initializeCheckoutPage() {
        const cartItemsStr = localStorage.getItem('cartItems');
        if (!cartItemsStr) return;

        try {
            const cartItems = JSON.parse(cartItemsStr);
            const validation = await this.validateCart(cartItems);

            if (!validation.valid && validation.errors) {
                // Hiển thị lỗi và remove items đã mua
                this.showCheckoutValidationErrors(validation.errors);
                this.removeInvalidItemsFromCart(validation.errors);
            }
        } catch (error) {
            console.error('❌ Error validating checkout cart:', error);
        }
    }

    /**
     * Hiển thị lỗi validation tại checkout
     */
    showCheckoutValidationErrors(errors) {
        const errorContainer = document.createElement('div');
        errorContainer.className = 'alert alert-warning mb-3';
        errorContainer.innerHTML = `
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Một số sản phẩm đã được loại bỏ</h6>
            <ul class="mb-0">
                ${errors.map(error => `<li>${error.message}</li>`).join('')}
            </ul>
        `;

        const checkoutForm = document.querySelector('.checkout-form, #checkout-form, .cart-container');
        if (checkoutForm) {
            checkoutForm.insertBefore(errorContainer, checkoutForm.firstChild);
        }
    }

    /**
     * Remove invalid items từ localStorage cart
     */
    removeInvalidItemsFromCart(errors) {
        const cartItemsStr = localStorage.getItem('cartItems');
        if (!cartItemsStr) return;

        try {
            let cartItems = JSON.parse(cartItemsStr);
            const invalidProductIds = errors.map(error => error.product_id);

            cartItems = cartItems.filter(item => !invalidProductIds.includes(item.product_id));

            localStorage.setItem('cartItems', JSON.stringify(cartItems));

            // Trigger cart update event
            window.dispatchEvent(new CustomEvent('cartUpdated', { detail: { cartItems } }));

        } catch (error) {
            console.error('❌ Error removing invalid cart items:', error);
        }
    }
}

// Global instance
window.cartValidator = new CartPurchaseValidator();

// Auto-initialize based on page
document.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;

    if (currentPath.includes('/shop')) {
        window.cartValidator.initializeShopPage();
    } else if (currentPath.includes('/cart') || currentPath.includes('/checkout')) {
        window.cartValidator.initializeCheckoutPage();
    }
});