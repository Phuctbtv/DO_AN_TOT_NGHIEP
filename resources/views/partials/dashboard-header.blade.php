{{--
    ĐẠI PHÚC - Shared Dashboard Header Partial
    Usage: @include('partials.dashboard-header', ['pageTitle' => '...', 'role' => '...'])
--}}
<header style="
    background:#fff;
    border-bottom:1px solid #e2e8f0;
    padding:.85rem 1.5rem;
    display:flex;
    align-items:center;
    justify-content:space-between;
    position:sticky;top:0;z-index:200;
    box-shadow:0 1px 4px rgba(0,0,0,.06);
">
    {{-- Left: Page title --}}
    <div style="display:flex;align-items:center;gap:1rem">
        {{-- Mobile hamburger (optional) --}}
        <div style="font-size:1.2rem;font-weight:700;color:#1e293b">
            {{ $pageTitle ?? 'Dashboard' }}
        </div>
    </div>

    {{-- Right: User dropdown (Alpine.js) --}}
    <div x-data="{ open: false }" style="position:relative">
        {{-- Avatar + name trigger --}}
        <button
            @click="open = !open"
            @click.outside="open = false"
            style="
                display:flex;align-items:center;gap:.6rem;
                background:transparent;border:none;cursor:pointer;
                padding:.4rem .7rem;border-radius:8px;
                transition:background .2s;
            "
            onmouseover="this.style.background='#f1f5f9'"
            onmouseout="this.style.background='transparent'"
        >
            {{-- Avatar circle --}}
            <div style="
                width:36px;height:36px;border-radius:50%;
                background:#0d9488;color:#fff;
                display:flex;align-items:center;justify-content:center;
                font-weight:700;font-size:.9rem;flex-shrink:0;
            ">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div style="text-align:left">
                <div style="font-size:.85rem;font-weight:600;color:#1e293b;white-space:nowrap">
                    Xin chào, <span style="color:#0d9488">{{ auth()->user()->name ?? 'Người dùng' }}</span>
                </div>
                <div style="font-size:.72rem;color:#64748b">
                    @php
                        $roleLabel = [
                            'admin'             => 'Quản trị viên',
                            'warehouse_manager' => 'Quản lý kho',
                            'driver'            => 'Tài xế',
                            'resident'          => 'Hộ dân',
                        ];
                        echo $roleLabel[auth()->user()->role ?? ''] ?? 'Người dùng';
                    @endphp
                </div>
            </div>
            {{-- Chevron --}}
            <svg style="width:14px;height:14px;color:#64748b;transition:transform .2s" :style="open ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Dropdown menu --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="
                position:absolute;right:0;top:calc(100% + 8px);
                background:#fff;border-radius:10px;
                box-shadow:0 8px 30px rgba(0,0,0,.12);
                min-width:200px;z-index:300;
                border:1px solid #e2e8f0;overflow:hidden;
            "
        >
            {{-- User info header --}}
            <div style="padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;background:#f8fafc">
                <div style="font-size:.8rem;font-weight:600;color:#1e293b">{{ auth()->user()->name }}</div>
                <div style="font-size:.72rem;color:#64748b;margin-top:.1rem">{{ auth()->user()->email }}</div>
            </div>

            {{-- Profile --}}
            <a href="{{ route('profile.edit') }}" style="
                display:flex;align-items:center;gap:.65rem;
                padding:.7rem 1rem;font-size:.85rem;color:#1e293b;
                text-decoration:none;transition:background .15s;
            "
            onmouseover="this.style.background='#f0fdfa';this.style.color='#0d9488'"
            onmouseout="this.style.background='transparent';this.style.color='#1e293b'"
            >
                <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Hồ sơ cá nhân
            </a>

            {{-- Divider --}}
            <div style="border-top:1px solid #f1f5f9"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="
                    width:100%;display:flex;align-items:center;gap:.65rem;
                    padding:.7rem 1rem;font-size:.85rem;color:#dc2626;
                    background:transparent;border:none;cursor:pointer;
                    text-align:left;transition:background .15s;
                "
                onmouseover="this.style.background='#fef2f2'"
                onmouseout="this.style.background='transparent'"
                >
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</header>
