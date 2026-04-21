<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Insta') }} | @yield('title')</title>

    {{-- fontawesomme --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Custum CSS --}}
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                   <h1 class="mb-0 h5">{{ config('app.name', 'Insta') }}</h1> 
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    {{-- [Soon] Serch Bar here. Show it when a user logs in. --}}
                    <ul class="navbar-nav me-auto">
                        @auth
                        {{-- admin ページじゃない時のみ表示する--}}
                            @if (!request()->is('admin/*')) 
                                <ul class="navbar-nav ms-auto">
                                    <form action="{{ route('search') }}" method="get" style="width: 300px">
                                        <input type="search" name="search" id="search" placeholder="Search people or #Hashtag" class="form-control form-control-sm">
                                    </form>

                            
                            {{-- 2. カテゴリー選択（中央） --}}
                                    <form action="{{ route('search.category') }}" method="get" class="d-flex align-items-center" style="width: 550px">
                                        <select name="category" class="form-select form-select-sm me-2" style="width: 180px">
                                            <option value="">All Categories</option>
                                            @foreach (\App\Models\Category::all() as $category)
                                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>


                            <button type="submit" class="btn btn-sm btn-outline-dark">
                                <i class="fas fa-search"></i>
                            </button>
                            </form>
                          </ul>
                          @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else

                            {{-- Suggested Users --}}
                            <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="{{ route('recommendations') }}" class="nav-link">
                                <i class="fa-solid fa-users text-dark icon-sm"></i>
                                </a>
                            </li>
                            </ul>

                            {{-- Home --}}
                            <li class="nav-item" title="Home">
                                <a href="{{ route('index') }}" class="nav-link">
                                    <i class="fas fa-house text-dark icon-sm"></i>
                                </a>
                            </li>

                            {{-- Create Post & Drafts Dropdown --}}
                        <li class="nav-item dropdown" title="Create or View Drafts">
                            <a href="#" id="create-dropdown" class="nav-link" data-bs-toggle="dropdown">
                                <i class="fas fa-circle-plus text-dark icon-sm"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="create-dropdown">
                                {{-- Create Post --}}
                                <a href="{{ route('post.create') }}" class="dropdown-item">
                                    <i class="fas fa-circle-plus me-2"></i>Create Post
                                </a>

                                {{-- Drafts Index  --}}
                                <a href="{{ route('post.drafts') }}" class="dropdown-item">
                                <i class="fas fa-file-pen me-2"></i>Drafts
                                </a>
                            </div>
                        </li>

                            <li class="nav-item dropdown">
                                    <button id="account-dropdown" class="btn shadow-none nav-link position-relative" data-bs-toggle="dropdown">
                                    {{-- アイコン部分 --}}
                                    @if (Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle avatar-sm">         
                                    @else
                                        <i class="fas fa-circle-user text-dark icon-sm"></i>
                                    @endif

                                    {{-- 通知カウントの計算（メッセージ ＋ 他の通知） --}}
                                   @php 
                                        // 自作の通知テーブルから未読数をカウント
                                        $notifCount = \App\Models\Notification::where('to_user_id', Auth::id())->where('is_read', false)->count();
                                        
                                        // メッセージの未読数
                                        $msgCount = Auth::user()->unreadMessages()->count();
                                        
                                        // 合計
                                        $totalCount = $notifCount + $msgCount; 
                                    @endphp

                                    @if($totalCount > 0)
                                        {{-- position-absolute で右上に固定 --}}
                                        <span class="position-absolute badge rounded-pill bg-danger" style="top: 10%; right: -5%; font-size: 0.65rem; padding: 0.2rem 0.4rem; line-height: 1;">
                                            {{ $totalCount > 9 ? '9+' : $totalCount }}
                                            <span class="visually-hidden">unread alerts</span>
                                        </span>
                                    @endif
                                </button>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    {{-- Admin Controls --}}
                                    {{-- @can - to use the GATE --}}
                                    @can('admin')
                                    <a href="{{ route('admin.users') }}" class="dropdown-item">
                                        <i class="fas fa-user-gear"></i> Admin
                                    </a>

                                    <hr class="dropdown-divider">
                                    @endcan

                                    {{-- Notifications --}}
                                     <a href="{{ route('notifications.index') }}" class="dropdown-item">
                                    <i class="fa-regular fa-bell"></i> Notifications
                                        {{-- count() を使って未読数を取得 --}}
                                        @if(Auth::user()->unreadMessages()->count() > 0)
                                            <span class="badge rounded-pill bg-danger">
                                                {{ Auth::user()->unreadMessages()->count() }}
                                            </span>
                                        @endif
                                    </a>

                                    {{-- Profile --}}
                                    <a href="{{ route('profile.show', Auth::user()->id) }}" class="dropdown-item">
                                        <i class="fas fa-circle-user"></i> Profile
                                    </a> 

                                    {{-- likes --}}
                                    <a href="{{ route('like.index') }}" class="dropdown-item">
                                        <i class="fas fa-solid fa-heart" style="font-size: 1rem;"></i> Likes
                                    </a>

                                    {{-- Chats --}}
                                    <a href="{{ route('chat.index') }}" class="dropdown-item">
                                        <i class="fa-regular fa-envelope"></i> Chat
                                        
                                        {{-- count() を使って未読数を取得 --}}
                                        @if(Auth::user()->unreadMessages()->count() > 0)
                                            <span class="badge rounded-pill bg-danger">
                                                {{ Auth::user()->unreadMessages()->count() }}
                                            </span>
                                        @endif
                                    </a>

                                    {{-- Mentions --}}
                                    <a href="{{ route('post.mentions') }}" class="dropdown-item d-flex justify-content-between align-items-center">
                                        <span><i class="fa-solid fa-at"></i> Mentions</span>

                                    {{-- Archives --}}
                                    <a href="{{ route('post.archive_index') }}" class="dropdown-item">
                                        <i class="fa-solid fa-clock-rotate-left" style="font-size: 1rem;"></i>Archives
                                    </a>

                                    {{-- Logout --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-right-from-bracket"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    @if (request()->is('admin/*'))
                    <div class="col-3">
                        <div class="list-group">
                            <a href="{{ route('admin.users') }}" class="list-group-item {{ request()->is('admin/users') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                            <a href="{{ route('admin.posts') }}" class="list-group-item {{ request()->is('admin/posts') ? 'active' : '' }}">
                                <i class="fas fa-newspaper"></i> Posts
                            </a>
                            <a href="{{ route('admin.categories') }}" class="list-group-item {{ request()->is('admin/categories') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> Categories
                            </a>
                        </div>
                    </div>
                @endif


                    <div class="col-9">
                    @yield('content')
                </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
