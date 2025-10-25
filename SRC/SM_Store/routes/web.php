<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\CartController;
use App\Http\Controllers\Account\PaymentController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\DashboardController;
use Kreait\Firebase\Contract\Auth;

Route::get('/', fn() => view('page.home.index'))->name('home')->middleware('load.user');

// Debug route để xem Firebase users
Route::get('/debug-users', function (Auth $auth) {
    try {
        $users = $auth->listUsers();

        echo "<h2>Firebase Users Debug</h2>";
        echo "<style>table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px;text-align:left} th{background-color:#f2f2f2}</style>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Display Name</th><th>UID</th><th>Role</th><th>Created</th></tr>";

        foreach ($users as $user) {
            $role = $user->customClaims['role'] ?? 'user';
            echo "<tr>";
            echo "<td>" . ($user->email ?? 'N/A') . "</td>";
            echo "<td><strong>" . ($user->displayName ?? 'N/A') . "</strong></td>";
            echo "<td>" . $user->uid . "</td>";
            echo "<td><strong style='color: " . ($role === 'saler' ? 'blue' : ($role === 'admin' ? 'red' : 'green')) . "'>" . $role . "</strong></td>";
            echo "<td>" . $user->metadata->createdAt->format('Y-m-d H:i:s') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Test route để tạo seller request và kiểm tra admin roles
Route::get('/test-admin-roles', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        $auth = app('Kreait\Firebase\Contract\Auth');
        
        echo "<h2>Test Admin Roles Page</h2>";
        
        // 1. Tạo test seller request
        $testData = [
            'user_id' => 'test_user_123',
            'reason' => 'Tôi muốn trở thành seller để bán sheet nhạc',
            'experience' => 'intermediate',
            'portfolio' => 'https://example.com/portfolio',
            'status' => 'pending',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        echo "<h3>1. Creating test seller request...</h3>";
        $result = $firestore->createDocument('seller_requests', $testData);
        echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "<br><br>";
        
        // 2. Kiểm tra seller requests
        echo "<h3>2. Checking seller requests...</h3>";
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "Pending requests: " . json_encode($requests, JSON_PRETTY_PRINT) . "<br><br>";
        
        // 3. Test UserRoleController logic
        echo "<h3>3. Testing UserRoleController logic...</h3>";
        $sellerRequests = [];
        
        if (isset($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                $fields = $doc['fields'] ?? [];
                $data = [];
                
                // Parse fields manually
                foreach ($fields as $key => $field) {
                    if (isset($field['stringValue'])) {
                        $data[$key] = $field['stringValue'];
                    } elseif (isset($field['doubleValue'])) {
                        $data[$key] = $field['doubleValue'];
                    } elseif (isset($field['integerValue'])) {
                        $data[$key] = $field['integerValue'];
                    } elseif (isset($field['booleanValue'])) {
                        $data[$key] = $field['booleanValue'];
                    } elseif (isset($field['timestampValue'])) {
                        $data[$key] = $field['timestampValue'];
                    }
                }
                
                $id = basename($doc['name'] ?? '');
                $data['id'] = $id;
                
                // Lấy email từ user (mock)
                $data['email'] = 'test@example.com';
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "Processed seller requests: " . json_encode($sellerRequests, JSON_PRETTY_PRINT) . "<br><br>";
        
        // 4. Link đến admin roles
        echo "<h3>4. Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page</a><br>";
        echo "<a href='/debug-seller-requests' target='_blank'>Debug Seller Requests</a><br>";
        
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Test route để tạo seller request
Route::get('/test-create-seller-request', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        
        $testData = [
            'user_id' => 'test_user_123',
            'reason' => 'Tôi muốn trở thành seller để bán sheet nhạc',
            'experience' => 'intermediate',
            'portfolio' => 'https://example.com/portfolio',
            'status' => 'pending',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        echo "<h2>Test Create Seller Request</h2>";
        echo "<pre>";
        echo "Test data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";
        
        $result = $firestore->createDocument('seller_requests', $testData);
        
        echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        // Kiểm tra lại
        $requests = $firestore->listDocuments('seller_requests', 100);
        echo "All requests after creation: " . json_encode($requests, JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Debug route để xem seller requests
Route::get('/debug-seller-requests', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        
        echo "<h2>Seller Requests Debug</h2>";
        echo "<style>table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px;text-align:left} th{background-color:#f2f2f2}</style>";
        
        // Lấy tất cả requests
        $allRequests = $firestore->listDocuments('seller_requests', 1000);
        echo "<h3>Tất cả requests trong collection:</h3>";
        echo "<table>";
        echo "<tr><th>Document ID</th><th>Fields</th></tr>";
        
        if (isset($allRequests['documents'])) {
            foreach ($allRequests['documents'] as $doc) {
                $id = basename($doc['name']);
                echo "<tr>";
                echo "<td>" . $id . "</td>";
                echo "<td><pre>" . json_encode($doc['fields'], JSON_PRETTY_PRINT) . "</pre></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Không có documents</td></tr>";
        }
        echo "</table>";
        
        // Query pending requests
        echo "<h3>Pending requests (query):</h3>";
        $pendingRequests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<table>";
        echo "<tr><th>Document ID</th><th>User ID</th><th>Status</th><th>Created At</th></tr>";
        
        if (isset($pendingRequests['documents'])) {
            foreach ($pendingRequests['documents'] as $doc) {
                $fields = $doc['fields'] ?? [];
                $data = [];
                
                foreach ($fields as $key => $field) {
                    if (isset($field['stringValue'])) {
                        $data[$key] = $field['stringValue'];
                    } elseif (isset($field['doubleValue'])) {
                        $data[$key] = $field['doubleValue'];
                    } elseif (isset($field['integerValue'])) {
                        $data[$key] = $field['integerValue'];
                    } elseif (isset($field['booleanValue'])) {
                        $data[$key] = $field['booleanValue'];
                    } elseif (isset($field['timestampValue'])) {
                        $data[$key] = $field['timestampValue'];
                    }
                }
                
                $id = basename($doc['name']);
                echo "<tr>";
                echo "<td>" . $id . "</td>";
                echo "<td>" . ($data['user_id'] ?? 'N/A') . "</td>";
                echo "<td>" . ($data['status'] ?? 'N/A') . "</td>";
                echo "<td>" . ($data['created_at'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Không có pending requests</td></tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/community', fn() => view('page.community.index'))->name('community.index')->middleware('load.user');
Route::get('/community/post/{id}', fn($id) => view('page.community.post-detail'))->name('community.post-detail')->middleware('load.user');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index')->middleware('load.user');
Route::post('/shop/filter', [ShopController::class, 'filter'])->name('shop.filter');

// ✅ Giỏ hàng & Thanh toán
// Route::get('/shop/cart', fn() => redirect()->route('account.cart'))->name('shop.cart')->middleware('load.user');
Route::get('/shop/checkout', [CheckoutController::class, 'showCheckout'])->name('shop.checkout')->middleware(['firebase.auth', 'load.user']);
Route::post('/shop/checkout/process', [CheckoutController::class, 'processCheckout'])->name('shop.checkout.process')->middleware(['firebase.auth', 'load.user']);

Route::get('/support', fn() => view('page.support.index'))->name('support.index')->middleware('load.user');

// Đăng nhập / Đăng ký / Quên mật khẩu
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

// ✅ SALER ROUTES
Route::prefix('saler')
    ->name('saler.') // ✅ Thêm dòng này
    ->middleware(['firebase.auth', 'role:saler', 'load.user'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.detail');
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::resource('products', \App\Http\Controllers\Seller\ProductController::class);

        // ✅ Route preview file — sẽ trở thành 'saler.products.preview-file'
        Route::post('products/preview-file', [App\Http\Controllers\Seller\ProductController::class, 'previewFile'])
            ->name('products.preview-file');

        Route::get('/profile', fn() => view('saler.profile.index'))->name('profile');
    });


// ✅ ADMIN ROUTES
Route::prefix('admin')
    ->middleware(['firebase.auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/anyf', [\App\Http\Controllers\Admin\SupportAccController::class, 'anyf'])->name('admin.anyf');
        Route::post('/anyf/{requestId}/approve', [\App\Http\Controllers\Admin\SupportAccController::class, 'approveRequest'])->name('admin.anyf.approve');
        Route::post('/anyf/{requestId}/reject', [\App\Http\Controllers\Admin\SupportAccController::class, 'rejectRequest'])->name('admin.anyf.reject');
        Route::get('/roles', [UserRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles/{uid}', [UserRoleController::class, 'updateRole'])->name('admin.roles.update');
        Route::post('/seller-requests/{requestId}/approve', [UserRoleController::class, 'approveSellerRequest'])->name('admin.seller-requests.approve');
        Route::post('/seller-requests/{requestId}/reject', [UserRoleController::class, 'rejectSellerRequest'])->name('admin.seller-requests.reject');
        Route::get('/users', fn() => view('admin.users.users'))->name('admin.users');
        Route::get('/analytics', fn() => view('admin.analytics.analytics'))->name('admin.analytics');
        
        // Admin Products Routes
        Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products');
        Route::get('/products/{id}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');
        
        // Admin Orders Routes
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
        Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
        Route::put('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        
        // Admin Posts Routes
        Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('admin.posts');
        Route::get('/posts/create', [\App\Http\Controllers\Admin\PostController::class, 'create'])->name('admin.posts.create');
        Route::post('/posts', [\App\Http\Controllers\Admin\PostController::class, 'store'])->name('admin.posts.store');
        Route::get('/posts/{id}/edit', [\App\Http\Controllers\Admin\PostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'update'])->name('admin.posts.update');
        Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('admin.posts.destroy');
        
        // Admin Settings Routes
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
    });

// ✅ ACCOUNT ROUTES
Route::prefix('account')
    ->middleware(['firebase.auth', 'load.user'])
    ->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('account.index');
        Route::get('/sheets', [AccountController::class, 'sheets'])->name('account.sheets');
        Route::get('/activity', [AccountController::class, 'activity'])->name('account.activity');
        Route::get('/settings', [AccountController::class, 'settings'])->name('account.settings');
        Route::put('/update', [AccountController::class, 'updateProfile'])->name('account.update');
        Route::get('/deposit', [AccountController::class, 'deposit'])->name('account.deposit');
        Route::get('/withdraw', [AccountController::class, 'withdraw'])->name('account.withdraw');
        Route::post('/withdraw/process', [AccountController::class, 'processWithdraw'])->name('account.withdraw.process');
        Route::get('/download/{id}', [AccountController::class, 'downloadSheet'])->name('account.download');
        Route::get('/cart', [CartController::class, 'index'])->name('account.cart');
        Route::get('/helpseller', [\App\Http\Controllers\Account\SellerRequestController::class, 'index'])->name('account.helpseller');
        Route::post('/seller-request', [\App\Http\Controllers\Account\SellerRequestController::class, 'store'])->name('account.seller.request');
    });

// Test route không cần authentication để kiểm tra seller requests
Route::get('/test-admin-no-auth', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        $auth = app('Kreait\Firebase\Contract\Auth');
        
        echo "<h2>Test Admin Logic (No Auth Required)</h2>";
        
        // Simulate the index method
        $users = [];
        foreach ($auth->listUsers() as $user) {
            if (!$user->email) continue;
            $users[] = [
                'uid'   => $user->uid,
                'email' => $user->email,
                'role'  => $user->customClaims['role'] ?? 'user',
            ];
        }
        
        echo "<h3>Users found: " . count($users) . "</h3>";
        
        // Test seller requests
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<h3>Raw seller requests from Firestore:</h3>";
        echo "<pre>" . json_encode($requests, JSON_PRETTY_PRINT) . "</pre>";
        
        // Process seller requests like in controller
        $sellerRequests = [];
        if (isset($requests['documents']) && is_array($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                // Kiểm tra cấu trúc dữ liệu từ logs
                if (isset($doc['data'])) {
                    // Cấu trúc mới từ FirestoreSimple
                    $data = $doc['data'];
                    $data['id'] = $doc['id'];
                } else {
                    // Cấu trúc cũ từ Firestore REST API
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    
                    // Parse fields manually
                    foreach ($fields as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        } elseif (isset($field['timestampValue'])) {
                            $data[$key] = $field['timestampValue'];
                        }
                    }
                    
                    $id = basename($doc['name'] ?? '');
                    $data['id'] = $id;
                }
                
                // Lấy email từ user
                try {
                    if (isset($data['user_id'])) {
                        $user = $auth->getUser($data['user_id']);
                        $data['email'] = $user->email ?? 'N/A';
                    } else {
                        $data['email'] = 'N/A';
                    }
                } catch (\Exception $e) {
                    $data['email'] = 'N/A';
                }
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "<h3>Processed seller requests: " . count($sellerRequests) . "</h3>";
        echo "<pre>" . json_encode($sellerRequests, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page (Requires Admin Role)</a><br>";
        echo "<a href='/test-simple' target='_blank'>Test Simple</a><br>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route để kiểm tra UserRoleController trực tiếp (không cần auth)
Route::get('/test-userrole-controller', function () {
    try {
        $controller = new \App\Http\Controllers\Admin\UserRoleController(app('Kreait\Firebase\Contract\Auth'));
        
        echo "<h2>Test UserRoleController Direct</h2>";
        
        // Gọi method index trực tiếp
        $response = $controller->index();
        
        echo "<h3>Controller response type: " . get_class($response) . "</h3>";
        
        if (method_exists($response, 'getData')) {
            $data = $response->getData();
            echo "<h3>View data:</h3>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        echo "<h3>Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page</a><br>";
        echo "<a href='/test-roles-logic' target='_blank'>Test Roles Logic</a><br>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route để kiểm tra seller requests trong UserRoleController
Route::get('/test-roles-logic', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        $auth = app('Kreait\Firebase\Contract\Auth');
        
        echo "<h2>Test UserRoleController Logic</h2>";
        
        // Test seller requests
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<h3>Raw seller requests from Firestore:</h3>";
        echo "<pre>" . json_encode($requests, JSON_PRETTY_PRINT) . "</pre>";
        
        // Process seller requests like in UserRoleController
        $sellerRequests = [];
        if (isset($requests['documents']) && is_array($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                echo "<h4>Processing document:</h4>";
                echo "<pre>" . json_encode($doc, JSON_PRETTY_PRINT) . "</pre>";
                
                // Kiểm tra cấu trúc dữ liệu từ logs
                if (isset($doc['data'])) {
                    echo "<p>Using doc['data'] structure</p>";
                    $data = $doc['data'];
                    $data['id'] = $doc['id'];
                } else {
                    echo "<p>Using doc['fields'] structure</p>";
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    
                    // Parse fields manually
                    foreach ($fields as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        } elseif (isset($field['timestampValue'])) {
                            $data[$key] = $field['timestampValue'];
                        }
                    }
                    
                    $id = basename($doc['name'] ?? '');
                    $data['id'] = $id;
                }
                
                // Lấy email từ user
                try {
                    if (isset($data['user_id'])) {
                        $user = $auth->getUser($data['user_id']);
                        $data['email'] = $user->email ?? 'N/A';
                    } else {
                        $data['email'] = 'N/A';
                    }
                } catch (\Exception $e) {
                    $data['email'] = 'N/A';
                }
                
                echo "<h4>Parsed data:</h4>";
                echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "<h3>Final processed requests: " . count($sellerRequests) . "</h3>";
        echo "<pre>" . json_encode($sellerRequests, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page</a><br>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route để kiểm tra UserRoleController trực tiếp
Route::get('/test-controller', function () {
    try {
        $controller = new \App\Http\Controllers\Admin\UserRoleController(app('Kreait\Firebase\Contract\Auth'));
        
        echo "<h2>Test UserRoleController</h2>";
        
        // Gọi method index trực tiếp
        $response = $controller->index();
        
        echo "<h3>Controller response:</h3>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route đơn giản để kiểm tra seller requests
Route::get('/test-simple', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        
        echo "<h2>Simple Seller Requests Test</h2>";
        
        // Query pending requests
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<h3>Raw query result:</h3>";
        echo "<pre>" . json_encode($requests, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Count: " . (isset($requests['documents']) ? count($requests['documents']) : 0) . "</h3>";
        
        if (isset($requests['documents'])) {
            foreach ($requests['documents'] as $i => $doc) {
                echo "<h4>Document " . ($i + 1) . ":</h4>";
                echo "<pre>" . json_encode($doc, JSON_PRETTY_PRINT) . "</pre>";
            }
        }
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
});

// Test route để kiểm tra seller requests parsing
Route::get('/test-seller-parsing', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        
        echo "<h2>Test Seller Requests Parsing</h2>";
        
        // Query pending requests
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<h3>Raw query result:</h3>";
        echo "<pre>" . json_encode($requests, JSON_PRETTY_PRINT) . "</pre>";
        
        // Test parsing logic
        $sellerRequests = [];
        if (isset($requests['documents']) && is_array($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                echo "<h4>Processing document:</h4>";
                echo "<pre>" . json_encode($doc, JSON_PRETTY_PRINT) . "</pre>";
                
                // Kiểm tra cấu trúc dữ liệu
                if (isset($doc['data'])) {
                    echo "<p>Using doc['data'] structure</p>";
                    $data = $doc['data'];
                    $data['id'] = $doc['id'];
                } else {
                    echo "<p>Using doc['fields'] structure</p>";
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    
                    foreach ($fields as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        } elseif (isset($field['timestampValue'])) {
                            $data[$key] = $field['timestampValue'];
                        }
                    }
                    
                    $id = basename($doc['name'] ?? '');
                    $data['id'] = $id;
                }
                
                echo "<h4>Parsed data:</h4>";
                echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "<h3>Final processed requests:</h3>";
        echo "<pre>" . json_encode($sellerRequests, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page</a><br>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route để kiểm tra hệ thống doanh thu admin
Route::get('/test-admin-revenue', function () {
    try {
        $revenueService = new \App\Services\AdminRevenueService();
        
        echo "<h2>🧪 Test Admin Revenue System</h2>";
        
        // 1. Tính toán doanh thu
        echo "<h3>1. Calculating admin revenue...</h3>";
        $revenueData = $revenueService->calculateAdminRevenue();
        
        echo "<h4>📊 Revenue Summary:</h4>";
        echo "<ul>";
        echo "<li><strong>Tổng doanh thu admin:</strong> " . number_format($revenueData['total_revenue']) . " VND</li>";
        echo "<li><strong>Tổng doanh số bán hàng:</strong> " . number_format($revenueData['total_sales']) . " VND</li>";
        echo "<li><strong>Số giao dịch hoàn thành:</strong> " . $revenueData['completed_purchases'] . "</li>";
        echo "<li><strong>Tỷ lệ hoa hồng:</strong> " . $revenueData['commission_rate'] . "%</li>";
        echo "<li><strong>Doanh thu hôm nay:</strong> " . number_format($revenueData['today_revenue']) . " VND</li>";
        echo "<li><strong>Doanh thu tuần này:</strong> " . number_format($revenueData['week_revenue']) . " VND</li>";
        echo "<li><strong>Doanh thu tháng này:</strong> " . number_format($revenueData['month_revenue']) . " VND</li>";
        echo "</ul>";
        
        // 2. Top Sellers
        echo "<h4>🏆 Top Sellers:</h4>";
        if (!empty($revenueData['revenue_by_seller'])) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Rank</th><th>Seller Name</th><th>Revenue</th><th>Sales</th><th>Transactions</th></tr>";
            foreach ($revenueData['revenue_by_seller'] as $index => $seller) {
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . $seller['seller_name'] . "</td>";
                echo "<td>" . number_format($seller['revenue']) . " VND</td>";
                echo "<td>" . number_format($seller['sales']) . " VND</td>";
                echo "<td>" . $seller['transactions'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Chưa có seller nào có doanh thu</p>";
        }
        
        // 3. Recent Transactions
        echo "<h4>💳 Recent Transactions:</h4>";
        if (!empty($revenueData['recent_transactions'])) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Product</th><th>Seller</th><th>Price</th><th>Commission</th><th>Date</th></tr>";
            foreach ($revenueData['recent_transactions'] as $transaction) {
                echo "<tr>";
                echo "<td>" . $transaction['product_name'] . "</td>";
                echo "<td>" . $transaction['seller_name'] . "</td>";
                echo "<td>" . number_format($transaction['price']) . " VND</td>";
                echo "<td>" . number_format($transaction['admin_commission']) . " VND</td>";
                echo "<td>" . $transaction['date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Chưa có giao dịch nào</p>";
        }
        
        // 4. Chart Data
        echo "<h4>📈 Chart Data (7 days):</h4>";
        echo "<pre>" . json_encode($revenueData['chart_data'], JSON_PRETTY_PRINT) . "</pre>";
        
        // 5. Revenue by Day
        echo "<h4>📅 Revenue by Day:</h4>";
        if (!empty($revenueData['revenue_by_day'])) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Date</th><th>Revenue</th><th>Sales</th><th>Transactions</th></tr>";
            foreach ($revenueData['revenue_by_day'] as $day) {
                echo "<tr>";
                echo "<td>" . $day['date'] . "</td>";
                echo "<td>" . number_format($day['revenue']) . " VND</td>";
                echo "<td>" . number_format($day['sales']) . " VND</td>";
                echo "<td>" . $day['transactions'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Chưa có dữ liệu theo ngày</p>";
        }
        
        // 6. Links
        echo "<h3>6. Test Links:</h3>";
        echo "<a href='/admin/dashboard' target='_blank'>🔗 Admin Dashboard (Requires Admin Login)</a><br>";
        echo "<a href='/test-create-seller-request' target='_blank'>🔗 Create Test Seller Request</a><br>";
        echo "<a href='/debug-seller-requests' target='_blank'>🔗 Debug Seller Requests</a><br>";
        
        echo "<h3>7. Summary:</h3>";
        echo "✅ Admin revenue system is ready!<br>";
        echo "✅ Revenue calculated from purchases with 30% commission<br>";
        echo "✅ Dashboard shows revenue charts and statistics<br>";
        echo "✅ Top sellers and recent transactions displayed<br>";
        
    } catch (Exception $e) {
        echo "<h3>❌ Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Test route để kiểm tra hệ thống seller request hoàn chỉnh
Route::get('/test-seller-system', function () {
    try {
        $firestore = new \App\Services\FirestoreSimple();
        $auth = app('Kreait\Firebase\Contract\Auth');
        
        echo "<h2>🧪 Test Seller Request System</h2>";
        
        // 1. Tạo test seller request
        echo "<h3>1. Creating test seller request...</h3>";
        $testData = [
            'user_id' => 'test_user_' . time(),
            'reason' => 'Tôi muốn trở thành seller để bán sheet nhạc và chia sẻ đam mê âm nhạc của mình với cộng đồng.',
            'experience' => 'intermediate',
            'portfolio' => 'https://example.com/portfolio',
            'status' => 'pending',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $result = $firestore->createDocument('seller_requests', $testData);
        echo "✅ Created seller request: " . json_encode($result, JSON_PRETTY_PRINT) . "<br><br>";
        
        // 2. Kiểm tra seller requests
        echo "<h3>2. Checking seller requests...</h3>";
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "📋 Pending requests count: " . (isset($requests['documents']) ? count($requests['documents']) : 0) . "<br>";
        
        // 3. Test UserRoleController logic
        echo "<h3>3. Testing UserRoleController logic...</h3>";
        $sellerRequests = [];
        
        if (isset($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                // Parse dữ liệu
                if (isset($doc['data'])) {
                    $data = $doc['data'];
                    $data['id'] = $doc['id'];
                } else {
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    
                    foreach ($fields as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        } elseif (isset($field['timestampValue'])) {
                            $data[$key] = $field['timestampValue'];
                        }
                    }
                    
                    $id = basename($doc['name'] ?? '');
                    $data['id'] = $id;
                }
                
                // Mock email
                $data['email'] = 'test@example.com';
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "✅ Processed seller requests: " . count($sellerRequests) . "<br>";
        
        // 4. Test SellerRequestController logic
        echo "<h3>4. Testing SellerRequestController logic...</h3>";
        $userId = 'test_user_' . time();
        
        // Test kiểm tra existing request
        $existingRequest = $firestore->queryDocuments('seller_requests', [
            ['field' => 'user_id', 'operator' => '==', 'value' => $userId],
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "🔍 Existing pending requests for user: " . (isset($existingRequest['documents']) ? count($existingRequest['documents']) : 0) . "<br>";
        
        // 5. Links để test
        echo "<h3>5. Test Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>🔗 Admin Roles Page (Requires Admin Login)</a><br>";
        echo "<a href='/account/helpseller' target='_blank'>🔗 User Helpseller Page (Requires User Login)</a><br>";
        echo "<a href='/test-create-seller-request' target='_blank'>🔗 Create Test Seller Request</a><br>";
        echo "<a href='/debug-seller-requests' target='_blank'>🔗 Debug Seller Requests</a><br>";
        
        echo "<h3>6. Summary:</h3>";
        echo "✅ Seller request system is ready!<br>";
        echo "✅ Users can submit seller requests at /account/helpseller<br>";
        echo "✅ Admins can manage requests at /admin/roles<br>";
        echo "✅ System handles pending, approved, and rejected states<br>";
        
    } catch (Exception $e) {
        echo "<h3>❌ Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Debug route để test admin roles page
Route::get('/debug-admin-roles', function () {
    try {
        $controller = new \App\Http\Controllers\Admin\UserRoleController(app('Kreait\Firebase\Contract\Auth'));
        
        echo "<h2>Debug Admin Roles Page</h2>";
        
        // Simulate the index method
        $users = [];
        $auth = app('Kreait\Firebase\Contract\Auth');
        foreach ($auth->listUsers() as $user) {
            if (!$user->email) continue;
            $users[] = [
                'uid'   => $user->uid,
                'email' => $user->email,
                'role'  => $user->customClaims['role'] ?? 'user',
            ];
        }
        
        echo "<h3>Users found:</h3>";
        echo "<pre>" . json_encode($users, JSON_PRETTY_PRINT) . "</pre>";
        
        // Test seller requests
        $firestore = new \App\Services\FirestoreSimple();
        $requests = $firestore->queryDocuments('seller_requests', [
            ['field' => 'status', 'operator' => '==', 'value' => 'pending']
        ]);
        
        echo "<h3>Raw seller requests from Firestore:</h3>";
        echo "<pre>" . json_encode($requests, JSON_PRETTY_PRINT) . "</pre>";
        
        // Process seller requests like in controller
        $sellerRequests = [];
        if (isset($requests['documents'])) {
            foreach ($requests['documents'] as $doc) {
                $fields = $doc['fields'] ?? [];
                $data = [];
                
                // Parse fields manually
                foreach ($fields as $key => $field) {
                    if (isset($field['stringValue'])) {
                        $data[$key] = $field['stringValue'];
                    } elseif (isset($field['doubleValue'])) {
                        $data[$key] = $field['doubleValue'];
                    } elseif (isset($field['integerValue'])) {
                        $data[$key] = $field['integerValue'];
                    } elseif (isset($field['booleanValue'])) {
                        $data[$key] = $field['booleanValue'];
                    } elseif (isset($field['timestampValue'])) {
                        $data[$key] = $field['timestampValue'];
                    }
                }
                
                $id = basename($doc['name'] ?? '');
                $data['id'] = $id;
                
                // Lấy email từ user
                try {
                    $user = $auth->getUser($data['user_id']);
                    $data['email'] = $user->email ?? 'N/A';
                } catch (\Exception $e) {
                    $data['email'] = 'N/A';
                }
                
                $sellerRequests[] = $data;
            }
        }
        
        echo "<h3>Processed seller requests:</h3>";
        echo "<pre>" . json_encode($sellerRequests, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Links:</h3>";
        echo "<a href='/admin/roles' target='_blank'>Go to Admin Roles Page</a><br>";
        echo "<a href='/test-create-seller-request' target='_blank'>Create Test Seller Request</a><br>";
        
    } catch (Exception $e) {
        echo "<h3>Error:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// ✅ PAYCART & PAYMENT
Route::get('/account/paycart', fn() => view('account.paycart'))->name('account.paycart')->middleware(['firebase.auth', 'load.user']);
Route::post('/paycart/confirm', [PaymentController::class, 'confirmCartPayment'])
    ->name('account.paycart.confirm')
    ->middleware(['firebase.auth', 'load.user']);


// API lấy số dư

Route::get('/api/user/balance', [App\Http\Controllers\Account\PaymentController::class, 'getUserBalance'])
    ->middleware(['firebase.auth']);

Route::prefix('payment')
    ->middleware(['firebase.auth', 'load.user'])
    ->group(function () {
        Route::get('/deposit', [PaymentController::class, 'showDepositForm'])->name('payment.deposit');
        Route::post('/deposit/create', [PaymentController::class, 'createDeposit'])->name('payment.deposit.create');
        Route::get('/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check.status');
    });

// ✅ WEBHOOK (không cần auth)
Route::post('/api/sepay/webhook', [PaymentController::class, 'handleWebhook']);

// ✅ CART API (auth)
Route::prefix('api/cart')
    ->middleware(['firebase.auth'])
    ->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/remove', [CartController::class, 'removeFromCart']);
        Route::post('/update', [CartController::class, 'updateQuantity']);
        Route::post('/clear', [CartController::class, 'clearCart']);
    });