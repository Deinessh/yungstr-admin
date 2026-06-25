@props(['sets' => [], 'class' => 'text-sm text-gray-600'])

@if(!empty($sets))
<div {{ $attributes->merge(['class' => $class]) }}>
    @foreach($sets as $index => $set)
        @php
            $names = collect($set)->pluck('name')->filter()->values();
        @endphp
        @if($names->isNotEmpty())
            <div @class(['mb-1' => !$loop->last])>
                @if(count($sets) > 1)
                    <span class="font-semibold text-brand-dark">Combo {{ $index + 1 }}:</span>
                @else
                    <span class="font-semibold text-brand-dark">Your picks:</span>
                @endif
                {{ $names->implode(', ') }}
            </div>
        @endif
    @endforeach
</div>
@endif
