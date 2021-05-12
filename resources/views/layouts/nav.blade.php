<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">

    <a class="navbar-brand" href="{{ url('/') }}">
        {{ config('app.name', 'Laravel') }}
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto">
            <li class="dropdown nav-item">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true">
                    Browse <span class="caret"></span>
                </a>

                <ul class="dropdown-menu">
                    <li class="nav-item"><a class="nav-link" href="/threads">All Threads</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="/threads?by={{ auth()->user()->name }}">My Threads</a></li>
                    @endauth

                    <li class="nav-item">
                        <a class="nav-link" href="/threads?popular=1">Popular All Time</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/threads?unanswered=1">Unanswered Threads</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown nav-item">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-has-popup="true" aria-expanded="false">Channels <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    @foreach($channels as $channel)
                        <li><a class="nav-link" href="/threads/{{ $channel->slug }}">{{$channel->name}}</a></li>
                    @endforeach
                </ul>
            </li>

            <li class="nav-item">
                <a href="/threads/create" class="nav-link">New Thread</a>
            </li>
        </ul>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
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
                <user-notifications></user-notifications>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('profile',Auth::user()) }}">My Profile</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>
