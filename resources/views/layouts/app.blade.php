<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ReWear - Buy and Sell Pre-loved Items')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="logo">
                ReWear
            </a>
            
            <!-- Search Bar -->
            <div class="search-container">
                <form action="{{ route('products.search') }}" method="GET" class="search-form">
                    <div class="search-input-wrapper">
                        <input type="text" 
                               name="q" 
                               placeholder="Find Cars, Mobile Phones and more..." 
                               class="search-input"
                               id="main-search-input"
                               value="{{ request('q') }}">
                        <div id="main-search-suggestions" class="search-suggestions"></div>
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
            </div>
            
            <!-- Right Navigation -->
            
            
            <div class="nav-right">
                <a href="{{ route('messages.index') }}" class="nav-item">
                    <i class="far fa-comment-alt"></i>
                    <span>Chat</span>
                    @php
                        $unreadCount = Auth::check() ? 
                            App\Models\Message::where('receiver_id', Auth::id())
                                              ->where('read', false)
                                              ->count() : 0;
                    @endphp
                    
                    @if($unreadCount > 0)
                        <span class="cart-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                
                <a href="{{ route('cart.index') }}" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="cart-badge">{{ count(session('cart')) }}</span>
                    @endif
                </a>

                
                
                @auth
                    <div class="nav-item dropdown">
                        <a href="{{ route('profile.show') }}" class="user-menu">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" class="avatar">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="{{ route('products.index', ['user' => Auth::id()]) }}" class="dropdown-item">
                                <i class="fas fa-tag"></i> My Listings
                            </a>
                            <a href="{{ route('orders.index') }}" class="dropdown-item">
                                <i class="fas fa-shopping-bag"></i> My Orders
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-item">
                        <i class="fas fa-user"></i>
                        <span>Login</span>
                    </a>
                @endauth
                
                <a href="{{ route('products.create') }}" class="sell-btn">
                    <i class="fas fa-plus"></i>
                    <span>SELL</span>
                </a>
                
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3 class="footer-title">ReWear</h3>
                <p class="footer-desc">India's largest marketplace to buy and sell used goods</p>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">Popular Categories</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('products.index', ['category' => 'vehicles']) }}">Cars</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'properties']) }}">Properties</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'electronics']) }}">Mobile Phones</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'jobs']) }}">Jobs</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">Popular Cities</h3>
                <ul class="footer-links">
                    <li><a href="#">Delhi</a></li>
                    <li><a href="#">Mumbai</a></li>
                    <li><a href="#">Bangalore</a></li>
                    <li><a href="#">Chennai</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">ReWear</h3>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">ReWear People</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-copyright">
            <p>&copy; {{ date('Y') }} ReWear. All rights reserved.</p>
        </div>
    </footer>

    @yield('scripts')
    @stack('scripts')
    
    <script>
        // Toggle dropdown menu
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.user-menu');
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                
                if (menu && dropdownMenu) {
                    menu.addEventListener('click', function(e) {
                        e.preventDefault();
                        dropdownMenu.classList.toggle('show');
                    });
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                dropdowns.forEach(dropdown => {
                    const menu = dropdown.querySelector('.user-menu');
                    const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                    
                    if (menu && dropdownMenu && !menu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            });
            
            // Main search suggestions functionality
            const mainSearchInput = document.getElementById('main-search-input');
            const mainSuggestions = document.getElementById('main-search-suggestions');
            let mainSearchTimeout;
            
            if (mainSearchInput && mainSuggestions) {
                mainSearchInput.addEventListener('input', function() {
                    clearTimeout(mainSearchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        mainSuggestions.style.display = 'none';
                        return;
                    }
                    
                    mainSearchTimeout = setTimeout(() => {
                        fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                displayMainSuggestions(data);
                            })
                            .catch(error => {
                                console.error('Error fetching suggestions:', error);
                            });
                    }, 300);
                });
                
                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mainSearchInput.contains(e.target) && !mainSuggestions.contains(e.target)) {
                        mainSuggestions.style.display = 'none';
                    }
                });
            }
            
            function displayMainSuggestions(data) {
                let html = '';
                let count = 0;
                const maxSuggestions = 8;
                
                if (data.products && data.products.length > 0) {
                    data.products.slice(0, 4).forEach(product => {
                        if (count < maxSuggestions) {
                            html += `<div class="suggestion-item" onclick="selectMainSuggestion('${product.replace(/'/g, "'")}')">
                                <i class="fas fa-search"></i> ${product}
                            </div>`;
                            count++;
                        }
                    });
                }
                
                if (data.categories && data.categories.length > 0) {
                    data.categories.slice(0, 2).forEach(category => {
                        if (count < maxSuggestions) {
                            html += `<div class="suggestion-item" onclick="selectMainSuggestion('${category.replace(/'/g, "'")}')">
                                <i class="fas fa-tag"></i> ${category}
                            </div>`;
                            count++;
                        }
                    });
                }
                
                if (data.locations && data.locations.length > 0) {
                    data.locations.slice(0, 2).forEach(location => {
                        if (count < maxSuggestions) {
                            html += `<div class="suggestion-item" onclick="selectMainSuggestion('${location.replace(/'/g, "'")}')">
                                <i class="fas fa-map-marker-alt"></i> ${location}
                            </div>`;
                            count++;
                        }
                    });
                }
                
                if (html) {
                    mainSuggestions.innerHTML = html;
                    mainSuggestions.style.display = 'block';
                } else {
                    mainSuggestions.style.display = 'none';
                }
            }
        });
        
        function selectMainSuggestion(suggestion) {
            document.getElementById('main-search-input').value = suggestion;
            document.getElementById('main-search-suggestions').style.display = 'none';
        }
    </script>
</body>
</html>