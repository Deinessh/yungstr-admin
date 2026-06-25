@extends('admin.layout')

@section('title', 'Referral Settings')
@section('heading', 'Referral Settings')

@section('content')
<form method="POST" action="{{ route('admin.referral-settings.update') }}" class="w-full max-w-3xl card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')

    <p class="text-sm text-brand-brown/70">Configure how referral codes are issued, unlocked, and rewarded — matching your referral program flow.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Referrals Required to Activate Coupon</label>
            <input type="number" name="referrals_required_to_unlock" value="{{ old('referrals_required_to_unlock', $settings['referrals_required_to_unlock']) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Reward Expiry After Activation (days)</label>
            <input type="number" name="referral_reward_expiry_days" value="{{ old('referral_reward_expiry_days', $settings['referral_reward_expiry_days']) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Reward Discount Type</label>
            <select name="referral_reward_type" class="input-field">
                <option value="fixed" @selected($settings['referral_reward_type'] === 'fixed')>Fixed Amount (₹)</option>
                <option value="percent" @selected($settings['referral_reward_type'] === 'percent')>Percent (%)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Reward Discount Value</label>
            <input type="number" step="0.01" name="referral_reward_discount" value="{{ old('referral_reward_discount', $settings['referral_reward_discount']) }}" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Discount on the referrer’s personal coupon after enough friends sign up.</p>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save Referral Settings</button>
</form>
@endsection
