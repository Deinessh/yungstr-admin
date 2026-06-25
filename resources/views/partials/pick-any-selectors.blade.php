@props([
    'cartKey',
    'quantity' => 1,
    'selectableProducts',
    'sets' => [],
    'inputPrefix' => 'pick_any',
])

@php
$choicesPerSet = \App\Services\PickAnyComboService::CHOICES_PER_SET;
$oldInput = old($inputPrefix.'.'.$cartKey, []);
@endphp

<div class="space-y-4">
    @for($setIndex = 0; $setIndex < $quantity; $setIndex++)
        @php
            $setValues = $sets[$setIndex] ?? [];
            $oldSet = $oldInput[$setIndex] ?? [];
        @endphp
        <div
            x-data="pickAnyUniqueSelects()"
            class="rounded-xl border border-amber-100 bg-amber-50/40 p-4 space-y-3"
        >
            @if($quantity > 1)
                <p class="text-sm font-bold text-brand-dark">Combo {{ $setIndex + 1 }} — choose 3 different products</p>
            @else
                <p class="text-sm font-bold text-brand-dark">Choose exactly 3 different products</p>
            @endif
            <p class="text-xs text-gray-500">Each product can only be selected once in this combo.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @for($choice = 0; $choice < $choicesPerSet; $choice++)
                    @php
                        $selectedId = old(
                            "{$inputPrefix}.{$cartKey}.{$setIndex}.{$choice}",
                            $oldSet[$choice] ?? ($setValues[$choice]['product_id'] ?? null)
                        );
                    @endphp
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Product {{ $choice + 1 }}</label>
                        <select
                            name="{{ $inputPrefix }}[{{ $cartKey }}][{{ $setIndex }}][{{ $choice }}]"
                            required
                            class="input-field text-sm"
                        >
                            <option value="">Select product</option>
                            @foreach($selectableProducts as $selectable)
                                <option value="{{ $selectable->id }}" @selected((string) $selectedId === (string) $selectable->id)>
                                    {{ $selectable->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
</div>
