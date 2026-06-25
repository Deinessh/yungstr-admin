@props(['name', 'bg' => 'from-[#7A6146] to-[#4D3925]', 'border' => 'border-amber-800', 'label' => null])

@php
    $displayLabel = $label ?? strtoupper($name);
@endphp

<div class="bg-[#FAF8F2] rounded-xl p-2 mb-3 h-36 flex flex-col justify-between relative">
    <div class="w-full bg-gradient-to-b {{ $bg }} text-white p-1 rounded h-28 flex flex-col justify-between text-center border {{ $border }}">
        <span class="text-[7px] bg-amber-400 font-bold px-1 rounded text-black block w-max mx-auto">S7</span>
        <span class="text-[9px] font-bold leading-tight block px-1">{{ $displayLabel }}</span>
        <div class="w-full h-1 bg-amber-400 rounded-full mt-1"></div>
    </div>
</div>
