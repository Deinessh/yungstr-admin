<x-mail::message>
# Great news, {{ $user->name }}!

You have successfully referred **{{ app(\App\Services\SettingService::class)->referralsRequiredToUnlock() }} people** — thank you for spreading the word!

Your personal coupon **{{ $coupon->code }}** is now **active**.

@if($coupon->type === 'percent')
It gives you **{{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off** your next order.
@else
It gives you **₹{{ number_format($coupon->value, 0) }} off** your next order.
@endif

**Important:** This coupon expires on **{{ $coupon->expires_at?->format('M d, Y') }}** (7 days from activation). Use it before it expires!

Apply it at checkout on your next order.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
