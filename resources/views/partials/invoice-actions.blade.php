@props([
    'order',
    'downloadRoute',
    'printRoute' => null,
    'size' => 'sm',
    'showPrint' => true,
    'showDownload' => true,
])

@php
$btnClass = $size === 'xs'
    ? 'inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1.5 rounded-lg border transition'
    : 'inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl border transition';
$outlineClass = $btnClass.' border-cream-dark bg-white text-brand-chocolate hover:bg-cream-bar';
$primaryClass = $btnClass.' border-brand-orange bg-brand-orange text-white hover:bg-brand-orange-dark';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-2']) }}>
    @if($showDownload)
    <a href="{{ route($downloadRoute, $order) }}" class="{{ $outlineClass }}">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>
    @endif
    @if($showPrint && $printRoute)
    <a href="{{ route($printRoute, $order) }}" target="_blank" rel="noopener" class="{{ $primaryClass }}">
        <i class="fas fa-print"></i> Print Receipt
    </a>
    @endif
</div>
