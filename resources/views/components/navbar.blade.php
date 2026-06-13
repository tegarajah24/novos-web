<nav class="navbar bg-base-100 shadow-md">
    <div class="flex-1">
        <a href="{{ route('staf.dashboard') }}" class="btn btn-ghost text-xl">{{ config('app.name', 'Novos') }}</a>
    </div>
    <div class="flex-none">
        <ul class="menu menu-horizontal px-1">
            @if(Auth::check())
                <li>
                    <details>
                        <summary>{{ Auth::user()->name }}</summary>
                        <ul class="bg-base-100 rounded-t-none p-2">
                            <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </details>
                </li>
            @else
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
            @endif
        </ul>
    </div>
</nav>