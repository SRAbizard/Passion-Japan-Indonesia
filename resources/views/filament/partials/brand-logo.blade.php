{{-- Panel brand logo: the PJi mark in the sidebar/topbar, the full
     Passion Japan Indonesia lockup on the login/register pages. Both PNGs
     carry the white outline, so they read on light and dark themes. --}}
@if(str_contains(request()->route()?->getName() ?? '', '.auth.'))
    <img src="{{ asset('images/logo-full.png') }}" alt="Passion Japan Indonesia" class="pj-brand-full" draggable="false">
@else
    <img src="{{ asset('images/logo-mark.png') }}" alt="Passion Japan Indonesia" class="pj-brand-mark" draggable="false">
@endif
