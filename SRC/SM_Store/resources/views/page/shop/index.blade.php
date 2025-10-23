
@extends('layouts.app')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>[x-cloak] { display: none !important; }</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filtering
    const categoryButtons = document.querySelectorAll('.category-btn');
    const currentCountry = '{{ $country }}';
    
    // Search elements
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchInfo = document.getElementById('search-info');
    
    let searchTimeout;
    let currentSearchCountry = currentCountry;
    
    // Set active category
    categoryButtons.forEach(btn => {
        const country = btn.getAttribute('data-country');
        if (country === currentCountry) {
            btn.classList.add('bg-blue-500', 'bg-opacity-80');
            btn.classList.remove('bg-white', 'bg-opacity-20');
        }
        
        btn.addEventListener('click', function() {
            const selectedCountry = this.getAttribute('data-country');
            currentSearchCountry = selectedCountry;
            filterProducts(selectedCountry, searchInput.value.trim());
        });
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        // Debounce search - wait 500ms after user stops typing
        searchTimeout = setTimeout(() => {
            filterProducts(currentSearchCountry, searchTerm);
        }, 500);
        
        // Show/hide clear button
        if (searchTerm) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
    });
    
    // Search button click
    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.trim();
        filterProducts(currentSearchCountry, searchTerm);
    });
    
    // Clear search
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        searchInfo.classList.add('hidden');
        filterProducts(currentSearchCountry, '');
    });
    
    // Enter key search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            filterProducts(currentSearchCountry, searchTerm);
        }
    });
    
    function filterProducts(country, search = '') {
        // Show loading
        const grid = document.getElementById('products-grid');
        grid.innerHTML = '<div class="col-span-full text-center py-12"><div class="text-white text-lg">🔄 Đang tải...</div></div>';
        
        // Make AJAX request
        fetch('{{ route("shop.filter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                country: country,
                search: search
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.products);
                updateActiveCategory(country);
                updateSearchInfo(search, data.count);
            } else {
                console.error('Filter error:', data.message);
                grid.innerHTML = '<div class="col-span-full text-center py-12"><div class="text-red-400 text-lg">❌ Có lỗi xảy ra</div></div>';
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            grid.innerHTML = '<div class="col-span-full text-center py-12"><div class="text-red-400 text-lg">❌ Lỗi kết nối</div></div>';
        });
    }
    
    function renderProducts(products) {
        const grid = document.getElementById('products-grid');
        
        if (products.length === 0) {
            const searchTerm = document.getElementById('search-input').value.trim();
            const searchMessage = searchTerm ? 
                `Không tìm thấy sheet nhạc nào có chứa "${searchTerm}"` : 
                'Không tìm thấy sheet nhạc nào phù hợp';
                
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="text-white text-lg mb-4">🎵 ${searchMessage}</div>
                    <p class="text-blue-200">Thử tìm kiếm với từ khóa khác hoặc chọn danh mục khác.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        products.forEach((product, index) => {
            const countryEmoji = getCountryEmoji(product.country_region || '');
            const imageSrc = product.image_path ? `{{ asset('') }}${product.image_path}` : `https://via.placeholder.com/320x180?text=${encodeURIComponent(product.name || 'Product')}`;
            const videoUrl = product.youtube_demo_url || 'https://www.youtube.com/embed/dQw4w9WgXcQ';
            
            // Highlight search terms in name and author
            const searchTerm = document.getElementById('search-input').value.trim();
            const highlightedName = highlightText(product.name || 'Chưa có tên', searchTerm);
            const highlightedAuthor = highlightText(product.author || 'Chưa xác định', searchTerm);
            
            html += `
                <div class="game-card rounded-xl p-4">
                    <div class="bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                        ${product.image_path ? 
                            `<img src="${imageSrc}" alt="${product.name || 'Sản phẩm'}" class="w-full h-full object-cover rounded-lg">` :
                            `<span class="text-3xl">${countryEmoji}</span>`
                        }
                    </div>
                    <h4 class="orbitron font-bold text-white mb-2">${highlightedName}</h4>
                    <p class="inter text-blue-200 text-sm mb-1">Tác giả: ${highlightedAuthor}</p>
                    <p class="inter text-blue-200 text-sm mb-1">Người soạn: ${product.transcribed_by || 'Admin'}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="orbitron text-yellow-300 font-bold">${new Intl.NumberFormat('vi-VN').format(product.price || 0)}đ</span>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors" 
                                onclick="openProductDetail('${escapeQuotes(product.name || 'Chưa có tên')}', '${escapeQuotes(product.author || 'Chưa xác định')}', '${escapeQuotes(product.transcribed_by || 'Admin')}', '${new Intl.NumberFormat('vi-VN').format(product.price || 0)}đ', '${imageSrc}', '${videoUrl}')">
                            Xem
                        </button>
                    </div>
                </div>
            `;
        });
        
        grid.innerHTML = html;
    }
    
    function updateActiveCategory(country) {
        categoryButtons.forEach(btn => {
            const btnCountry = btn.getAttribute('data-country');
            if (btnCountry === country) {
                btn.classList.add('bg-blue-500', 'bg-opacity-80');
                btn.classList.remove('bg-white', 'bg-opacity-20');
            } else {
                btn.classList.remove('bg-blue-500', 'bg-opacity-80');
                btn.classList.add('bg-white', 'bg-opacity-20');
            }
        });
    }
    
    function updateSearchInfo(searchTerm, count) {
        const searchInfo = document.getElementById('search-info');
        
        if (searchTerm.trim()) {
            searchInfo.innerHTML = `Tìm kiếm cho: "${searchTerm}" - ${count} kết quả`;
            searchInfo.classList.remove('hidden');
        } else {
            searchInfo.classList.add('hidden');
        }
    }
    
    function getCountryEmoji(countryRegion) {
        if (countryRegion.includes('Việt Nam')) return '🇻🇳';
        if (countryRegion.includes('Hàn Quốc')) return '🇰🇷';
        if (countryRegion.includes('Nhật Bản')) return '🇯🇵';
        if (countryRegion.includes('Trung Quốc')) return '🇨🇳';
        if (countryRegion.includes('Âu Mỹ')) return '🇺🇸';
        return '🎵';
    }
    
    function escapeQuotes(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }
    
    function highlightText(text, searchTerm) {
        if (!searchTerm.trim()) return text;
        
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="bg-yellow-300 text-black px-1 rounded">$1</mark>');
    }
});

// Global function for opening product detail
function openProductDetail(name, author, composer, price, img, video) {
    // Access Alpine.js data
    const shopDiv = document.getElementById('shop');
    if (shopDiv && shopDiv._x_dataStack) {
        shopDiv._x_dataStack[0].product = {
            name: name,
            author: author,
            composer: composer,
            price: price,
            img: img,
            video: video
        };
        shopDiv._x_dataStack[0].showDetail = true;
    }
}
</script>
@endpush

@section('title', 'Cửa hàng - Sky Music Store')

@section('content')
 <div id="shop" class="page-content" x-data="{ showDetail: false, product: {} }">
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="orbitron text-5xl font-bold text-white text-center mb-16">🎼 Cửa Hàng Sheet Nhạc</h2>
                
                <!-- Search Box -->
                <div class="max-w-md mx-auto mb-12">
                    <div class="relative">
                        <input type="text" 
                               id="search-input"
                               placeholder="Tìm kiếm theo tên bài hát hoặc tác giả..." 
                               value="{{ $search }}"
                               class="w-full px-6 py-4 rounded-full bg-white bg-opacity-20 backdrop-blur-sm text-white placeholder-gray-300 border border-white border-opacity-30 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent inter">
                        <button id="search-btn" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <button id="clear-search" 
                                class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-300 hover:text-white transition-colors {{ empty($search) ? 'hidden' : '' }}"
                                title="Xóa tìm kiếm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Search suggestions/results count -->
                    <div id="search-info" class="mt-2 text-center text-blue-200 text-sm {{ empty($search) ? 'hidden' : '' }}">
                        @if(!empty($search))
                            Tìm kiếm cho: "{{ $search }}" - {{ $products->count() }} kết quả
                        @endif
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="all">Tất Cả</button>
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="Việt Nam">Việt Nam</button>
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="Nhật Bản">Nhật Bản</button>
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="Hàn Quốc">Hàn Quốc</button>
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="Trung Quốc">Trung Quốc</button>
                    <button class="category-btn bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter" data-country="Âu Mỹ">US-UK</button>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse($products as $product)
                        <!-- Product {{ $loop->iteration }} -->
                        <div class="game-card rounded-xl p-4">
                            <div class="bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                                @if(!empty($product['image_path']))
                                    <img src="{{ asset($product['image_path']) }}" alt="{{ $product['name'] ?? 'Sản phẩm' }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <span class="text-3xl">
                                        @if(str_contains($product['country_region'] ?? '', 'Việt Nam'))
                                            🇻🇳
                                        @elseif(str_contains($product['country_region'] ?? '', 'Hàn Quốc'))
                                            🇰🇷
                                        @elseif(str_contains($product['country_region'] ?? '', 'Nhật Bản'))
                                            🇯🇵
                                        @elseif(str_contains($product['country_region'] ?? '', 'Trung Quốc'))
                                            🇨🇳
                                        @else
                                            🎵
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <h4 class="orbitron font-bold text-white mb-2">{{ $product['name'] ?? 'Chưa có tên' }}</h4>
                            <p class="inter text-blue-200 text-sm mb-1">Tác giả: {{ $product['author'] ?? 'Chưa xác định' }}</p>
                            <p class="inter text-blue-200 text-sm mb-1">Người soạn: {{ $product['transcribed_by'] ?? 'Admin' }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="orbitron text-yellow-300 font-bold">{{ number_format($product['price'] ?? 0) }}đ</span>
                                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors"
                                    @click="product = { 
                                        name: '{{ addslashes($product['name'] ?? 'Chưa có tên') }}', 
                                        author: '{{ addslashes($product['author'] ?? 'Chưa xác định') }}', 
                                        composer: '{{ addslashes($product['transcribed_by'] ?? 'Admin') }}', 
                                        price: '{{ number_format($product['price'] ?? 0) }}đ', 
                                        img: '{{ !empty($product['image_path']) ? asset($product['image_path']) : 'https://via.placeholder.com/320x180?text=' . urlencode($product['name'] ?? 'Product') }}', 
                                        video: '{{ $product['youtube_demo_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}',
                                        seller_id: '{{ $product['seller_id'] ?? '' }}',
                                        product_id: '{{ isset($product['id']) ? $product['id'] : '' }}'
                                    }; showDetail = true;">
                                    Xem
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-white text-lg mb-4">🎵 Chưa có sản phẩm nào</div>
                            <p class="text-blue-200">Hiện tại chưa có sheet nhạc nào được đăng bán.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->count() > 0)
                    <div class="flex justify-center mt-8">
                        <div class="flex gap-2">
                            @for($i = 1; $i <= ceil($totalProducts / $perPage); $i++)
                                <button class="px-4 py-2 rounded-lg {{ $currentPage == $i ? 'bg-blue-500 text-white' : 'bg-white bg-opacity-20 text-white hover:bg-opacity-30' }} transition-colors"
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => $i]) }}'">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </section>
    <!-- Popup chi tiết sản phẩm -->
    <div x-show="showDetail" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 relative flex flex-col gap-4">
                <button @click="showDetail=false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="w-full md:w-1/3">
                        <div class="rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                            <img :src="product.img" alt="Ảnh đại diện" class="object-cover w-full h-full" />
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <h3 class="orbitron text-2xl font-bold text-gray-900" x-text="product.name"></h3>
                        <p class="inter text-gray-700 text-base">Tác giả: <span class="font-semibold" x-text="product.author"></span></p>
                        <p class="inter text-gray-700 text-base">Người soạn: <span class="font-semibold" x-text="product.composer"></span></p>
                        <p class="orbitron text-blue-600 text-xl font-bold">Giá: <span x-text="product.price"></span></p>
                        <button class="bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-blue-600 transition w-fit mt-2" onclick="addToCart()">Thêm vào giỏ hàng</button>
                    </div>
                </div>
                <div class="mt-4">
                    <div style="position:relative;width:100%;aspect-ratio:16/9;">
                        <iframe :src="product.video" style="position:absolute;top:0;left:0;width:100%;height:100%;" class="rounded-lg shadow" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    <script>
    function showToast(message, type = 'info') {
        let toast = document.createElement('div');
        toast.className = `fixed top-6 right-6 z-[9999] px-6 py-3 rounded-lg shadow-lg text-white font-semibold text-base transition-all duration-300 ${type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => document.body.removeChild(toast), 400);
        }, 2000);
    }

    function addToCart() {
        var shopDiv = document.getElementById('shop');
        var currentUserId = '{{ $user_id }}';
        if (shopDiv && shopDiv._x_dataStack) {
            var p = shopDiv._x_dataStack[0].product;
            if (!p || !p.name) return;
            
            console.log('DEBUG addToCart:', { seller_id: p.seller_id, currentUserId: currentUserId, product: p });
            
            // Kiểm tra không thể mua sheet của chính mình
            if (p.seller_id && currentUserId && p.seller_id == currentUserId) {
                showToast('Bạn không thể thêm sheet nhạc của chính mình vào giỏ hàng!', 'error');
                return;
            }

            // Parse price (remove "đ" and convert to number)
            var priceStr = (p.price || '').replace(/\D/g, '');
            var priceNum = parseInt(priceStr) || 0;

            // Call API to add to cart
            fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: p.product_id,
                    name: p.name,
                    price: priceNum,
                    image: p.img || '',
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Đã thêm vào giỏ hàng!', 'success');
                    console.log('Cart updated:', data.cart);
                } else {
                    showToast(data.message || 'Không thể thêm vào giỏ hàng', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
            });
        }
    }
    </script>
@endsection