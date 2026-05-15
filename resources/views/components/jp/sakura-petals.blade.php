@props(['count' => 18])

{{-- Falling sakura petals overlay. Position parent as `relative` to contain. --}}
<div class="sakura-layer" aria-hidden="true">
    @for($i = 0; $i < $count; $i++)
        @php
            $left  = random_int(0, 95);
            $delay = random_int(0, 100) / 10;     // 0–10s
            $dur   = random_int(80, 180) / 10;    // 8–18s
            $size  = random_int(8, 20);
        @endphp
        <span class="sakura-petal"
              style="left: {{ $left }}%; width: {{ $size }}px; height: {{ $size }}px;
                     --fall-d: {{ $dur }}s; --fall-delay: {{ $delay }}s;"></span>
    @endfor
</div>
