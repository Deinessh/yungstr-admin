<x-mail::message>
# Congratulations, {{ $user->name }}!

Thank you for placing your first order with **Yungstr Club**.

Your personal referral coupon code is:

**{{ $coupon->code }}**

This coupon is **not active yet**. To activate it, refer **{{ app(\App\Services\SettingService::class)->referralsRequiredToUnlock() }} friends** who successfully create an account using your referral link:

{{ route('register') }}?ref={{ $coupon->code }}

Once activated, you can use this coupon at checkout. Only you can use this code — it cannot be shared for others to redeem.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
