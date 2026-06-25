@props([
    'name' => 'quantity',
    'value' => 1,
    'min' => 1,
    'max' => null,
    'size' => 'md',
    'autoSubmit' => false,
])

@php
$sizeClasses = $size === 'sm'
    ? 'h-9 min-w-[5.5rem] shrink-0'
    : 'h-12 min-w-[8rem]';
$buttonClasses = $size === 'sm'
    ? 'w-8 text-base'
    : 'w-10 text-xl';
$textClasses = $size === 'sm' ? 'text-sm' : 'text-base';
@endphp

<div
    x-data="{
        qty: {{ (int) $value }},
        min: {{ (int) $min }},
        max: {{ $max !== null ? (int) $max : 'null' }},
        submitTimer: null,
        decrease() {
            if (this.qty > this.min) {
                this.qty--;
                this.afterChange();
            }
        },
        increase() {
            if (this.max === null || this.qty < this.max) {
                this.qty++;
                this.afterChange();
            }
        },
        afterChange() {
            @if($autoSubmit)
            clearTimeout(this.submitTimer);
            this.submitTimer = setTimeout(() => {
                this.$el.closest('form')?.requestSubmit();
            }, 250);
            @endif
        }
    }"
    class="quantity-adjuster inline-flex items-center border border-gray-200 rounded-full overflow-hidden bg-white {{ $sizeClasses }}"
>
    <input type="hidden" name="{{ $name }}" :value="qty">

    <button
        type="button"
        @click="decrease()"
        :disabled="qty <= min"
        class="{{ $buttonClasses }} h-full shrink-0 text-brand-dark hover:bg-amber-50 font-bold transition disabled:opacity-30 disabled:pointer-events-none"
        aria-label="Decrease quantity"
    >−</button>

    <span
        x-text="qty"
        class="w-full text-center text-brand-dark font-semibold select-none {{ $textClasses }}"
        aria-live="polite"
    ></span>

    <button
        type="button"
        @click="increase()"
        :disabled="max !== null && qty >= max"
        class="{{ $buttonClasses }} h-full shrink-0 text-brand-dark hover:bg-amber-50 font-bold transition disabled:opacity-30 disabled:pointer-events-none"
        aria-label="Increase quantity"
    >+</button>
</div>
