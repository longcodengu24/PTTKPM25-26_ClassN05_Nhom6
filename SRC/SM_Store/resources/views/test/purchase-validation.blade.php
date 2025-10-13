<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Cart Purchase Validation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2><i class="fas fa-shopping-cart me-2"></i>Test Cart Purchase Validation</h2>
                
                <!-- User Session Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>User Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>User ID:</strong> <span id="user-id">{{ session('firebase_uid', 'Not logged in') }}</span></p>
                        <p><strong>Status:</strong> 
                            @if(session('firebase_uid'))
                                <span class="badge bg-success">Logged In</span>
                            @else
                                <span class="badge bg-danger">Not Logged In</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Test Product -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-music me-2"></i>Test Product</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Test Song for Sold Count Demo</h6>
                                <p class="text-muted">Test Author - 25,000đ</p>
                                <input type="hidden" id="product-id" value="s8ZVQUE9iHZLIPh9wVz6">
                                <input type="hidden" id="product-name" value="Test Song for Sold Count Demo">
                                <input type="hidden" id="product-price" value="25000">
                                <input type="hidden" id="seller-id" value="cfT4zfDX4YRkuwd4T6X3seJhtbl1">
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary" id="check-purchase-btn">
                                    <i class="fas fa-search me-1"></i>Check Purchase Status
                                </button>
                                <button class="btn btn-success mt-2" id="add-to-cart-btn" data-product-id="s8ZVQUE9iHZLIPh9wVz6" data-product-name="Test Song for Sold Count Demo">
                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-list me-2"></i>Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="results" class="text-muted">
                            Click buttons above to test functionality...
                        </div>
                    </div>
                </div>

                <!-- Cart Simulation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-basket me-2"></i>Cart Validation Test</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-warning" id="test-cart-validation">
                            <i class="fas fa-check-circle me-1"></i>Test Cart Validation
                        </button>
                        <button class="btn btn-info ms-2" id="load-purchased-products">
                            <i class="fas fa-list me-1"></i>Load Purchased Products
                        </button>
                        <div id="cart-results" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class PurchaseValidationTester {
            constructor() {
                this.productId = document.getElementById('product-id').value;
                this.productName = document.getElementById('product-name').value;
                this.productPrice = parseInt(document.getElementById('product-price').value);
                this.sellerId = document.getElementById('seller-id').value;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                this.initEventListeners();
            }

            initEventListeners() {
                document.getElementById('check-purchase-btn').addEventListener('click', () => {
                    this.checkPurchaseStatus();
                });

                document.getElementById('add-to-cart-btn').addEventListener('click', (e) => {
                    this.testAddToCart(e);
                });

                document.getElementById('test-cart-validation').addEventListener('click', () => {
                    this.testCartValidation();
                });

                document.getElementById('load-purchased-products').addEventListener('click', () => {
                    this.loadPurchasedProducts();
                });
            }

            async makeRequest(url, method = 'GET', data = null) {
                const options = {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                };

                if (data && method !== 'GET') {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(url, options);
                return await response.json();
            }

            async checkPurchaseStatus() {
                this.showLoading('Checking purchase status...');
                
                try {
                    const result = await this.makeRequest('/api/cart/can-add', 'POST', {
                        product_id: this.productId
                    });

                    this.showResult('Purchase Status Check', result);
                } catch (error) {
                    this.showError('Error checking purchase status: ' + error.message);
                }
            }

            async testAddToCart(e) {
                e.preventDefault();
                this.showLoading('Testing add to cart...');

                try {
                    const canAdd = await this.makeRequest('/api/cart/can-add', 'POST', {
                        product_id: this.productId
                    });

                    if (canAdd.can_add) {
                        this.showSuccess('✅ Product can be added to cart!');
                        // Simulate adding to cart
                        this.simulateCartAdd();
                    } else {
                        this.showWarning('❌ Cannot add to cart: ' + canAdd.message);
                        this.disableAddButton();
                    }
                } catch (error) {
                    this.showError('Error testing add to cart: ' + error.message);
                }
            }

            async testCartValidation() {
                this.showLoading('Testing cart validation...');

                const cartItems = [
                    {
                        product_id: this.productId,
                        name: this.productName,
                        seller_id: this.sellerId,
                        price: this.productPrice
                    }
                ];

                try {
                    const result = await this.makeRequest('/api/cart/validate', 'POST', {
                        cart_items: cartItems
                    });

                    this.showCartResult('Cart Validation Test', result);
                } catch (error) {
                    this.showError('Error validating cart: ' + error.message);
                }
            }

            async loadPurchasedProducts() {
                this.showLoading('Loading purchased products...');

                try {
                    const result = await this.makeRequest('/api/cart/purchased-products');
                    this.showCartResult('Purchased Products', result);
                } catch (error) {
                    this.showError('Error loading purchased products: ' + error.message);
                }
            }

            simulateCartAdd() {
                const btn = document.getElementById('add-to-cart-btn');
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Added to Cart';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-cart-plus me-1"></i>Add to Cart';
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                    btn.disabled = false;
                }, 3000);
            }

            disableAddButton() {
                const btn = document.getElementById('add-to-cart-btn');
                btn.innerHTML = '<i class="fas fa-ban me-1"></i>Already Purchased';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
                btn.disabled = true;
            }

            showLoading(message) {
                document.getElementById('results').innerHTML = `
                    <div class="text-primary">
                        <i class="fas fa-spinner fa-spin me-2"></i>${message}
                    </div>
                `;
            }

            showResult(title, result) {
                const statusClass = result.success ? 'success' : 'danger';
                const icon = result.success ? 'check-circle' : 'times-circle';
                
                document.getElementById('results').innerHTML = `
                    <div class="alert alert-${statusClass}">
                        <h6><i class="fas fa-${icon} me-2"></i>${title}</h6>
                        <div><strong>Status:</strong> ${result.success ? 'Success' : 'Failed'}</div>
                        <div><strong>Message:</strong> ${result.message}</div>
                        ${result.can_add !== undefined ? `<div><strong>Can Add:</strong> ${result.can_add ? 'Yes' : 'No'}</div>` : ''}
                        <hr>
                        <small><strong>Raw Response:</strong><br><code>${JSON.stringify(result, null, 2)}</code></small>
                    </div>
                `;
            }

            showCartResult(title, result) {
                const statusClass = result.success ? 'success' : 'danger';
                const icon = result.success ? 'check-circle' : 'times-circle';
                
                let content = `
                    <div class="alert alert-${statusClass}">
                        <h6><i class="fas fa-${icon} me-2"></i>${title}</h6>
                        <div><strong>Status:</strong> ${result.success ? 'Success' : 'Failed'}</div>
                        <div><strong>Message:</strong> ${result.message}</div>
                `;

                if (result.purchased_products) {
                    content += `<div><strong>Purchased Products:</strong> ${result.purchased_products.length} items</div>`;
                    if (result.purchased_products.length > 0) {
                        content += `<ul>`;
                        result.purchased_products.forEach(id => {
                            content += `<li><code>${id}</code></li>`;
                        });
                        content += `</ul>`;
                    }
                }

                if (result.errors) {
                    content += `<div><strong>Errors:</strong></div><ul>`;
                    result.errors.forEach(error => {
                        content += `<li>${error.message}</li>`;
                    });
                    content += `</ul>`;
                }

                content += `
                        <hr>
                        <small><strong>Raw Response:</strong><br><code>${JSON.stringify(result, null, 2)}</code></small>
                    </div>
                `;

                document.getElementById('cart-results').innerHTML = content;
            }

            showSuccess(message) {
                document.getElementById('results').innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>${message}
                    </div>
                `;
            }

            showWarning(message) {
                document.getElementById('results').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>${message}
                    </div>
                `;
            }

            showError(message) {
                document.getElementById('results').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>${message}
                    </div>
                `;
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            window.purchaseValidator = new PurchaseValidationTester();
        });
    </script>
</body>
</html>