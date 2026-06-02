<aside class="sidebar bg-base-200 min-h-screen w-64 p-4">
    <ul class="menu">
        @php
            $userRole = Auth::check() ? Auth::user()->role->name : null;
        @endphp
        
        @if($userRole === 'Admin' || $userRole === 'Manager' || $userRole === 'Super Admin')
            <li>
                <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Admin Dashboard
                </a>
            </li>
        @endif
        
        @if($userRole === 'Design' || $userRole === 'Super Admin')
            <li>
                <a href="{{ url('design/dashboard') }}" class="{{ request()->is('design*') ? 'active' : '' }}">
                    Design Dashboard
                </a>
            </li>
        @endif
        
        @if($userRole === 'Produksi' || $userRole === 'Super Admin')
            <li>
                <a href="{{ url('produksi/dashboard') }}" class="{{ request()->is('produksi*') ? 'active' : '' }}">
                    Produksi Dashboard
                </a>
            </li>
        @endif
        
        @if($userRole === 'Customer')
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
        @endif
    </ul>
</aside>