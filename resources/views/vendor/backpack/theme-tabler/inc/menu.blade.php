<!-- resources/views/vendor/backpack/theme-tabler/inc/menu.blade.php -->

{{-- ================= LEFT SIDE TOP MENU ================= --}}
@if (backpack_auth()->check())
<ul class="nav navbar-nav d-md-down-none">
    {{-- Topbar left content (Dashboard, Users, Bookings dropdown) --}}
    @include(backpack_view('inc.topbar_left_content'))
</ul>
@endif


{{-- ================= RIGHT SIDE TOP MENU ================= --}}
<ul class="nav navbar-nav d-flex flex-row flex-shrink-0
           @if(backpack_theme_config('html_direction') == 'rtl') me-0 @endif">

    @if (backpack_auth()->guest())

    {{-- Guest: Login --}}
    <li class="nav-item">
        <a class="nav-link" href="{{ route('backpack.auth.login') }}">
            {{ trans('backpack::base.login') }}
        </a>
    </li>

    {{-- Guest: Register --}}
    @if (config('backpack.base.registration_open'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('backpack.auth.register') }}">
            {{ trans('backpack::base.register') }}
        </a>
    </li>
    @endif

    @else

    {{-- Dark / Light mode switch --}}
    @if(backpack_theme_config('options.showColorModeSwitcher'))
    <li class="nav-item">
        @include(backpack_view('layouts.partials.switch_theme'))
    </li>
    @endif

    {{-- Alerts / Notifications --}}
    @include(backpack_view('inc.topbar_right_content'))

    {{-- User profile dropdown --}}
    @include(backpack_view('inc.menu_user_dropdown'))

    @endif
</ul>