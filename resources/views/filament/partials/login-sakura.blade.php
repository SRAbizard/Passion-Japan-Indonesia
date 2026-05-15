{{-- Fixed-position falling sakura petals across the entire login viewport.
     z-index 0 keeps petals behind the login card (which has higher stacking). --}}
<div class="pj-login-sakura" aria-hidden="true">
    @for($i = 0; $i < 14; $i++)
        @php
            $left  = random_int(0, 95);
            $delay = random_int(0, 100) / 10;
            $dur   = random_int(80, 180) / 10;
            $size  = random_int(8, 18);
        @endphp
        <span class="sakura-petal"
              style="left: {{ $left }}%; width: {{ $size }}px; height: {{ $size }}px;
                     --fall-d: {{ $dur }}s; --fall-delay: {{ $delay }}s;"></span>
    @endfor
</div>

<style>
    .pj-login-sakura {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }
</style>
