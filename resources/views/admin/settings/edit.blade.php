@extends('admin.layout')

@section('title', 'Store Settings')
@section('heading', 'Store Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[14rem_1fr] gap-6 items-start">
    <aside class="card p-3 lg:sticky lg:top-24">
        <nav class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0">
            @foreach($tabs as $key => $label)
            <a href="{{ route('admin.settings.edit', ['tab' => $key]) }}"
               class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition {{ $tab === $key ? 'bg-brand-orange text-white shadow-sm' : 'text-brand-chocolate hover:bg-cream-bar' }}">
                {{ $label }}
            </a>
            @endforeach
        </nav>
    </aside>

    <div class="min-w-0">
        @if($tab === 'general')
            @include('admin.settings.tabs.general')
        @elseif($tab === 'payments')
            @include('admin.settings.tabs.payments')
        @elseif($tab === 'theme')
            @include('admin.settings.tabs.theme')
        @elseif($tab === 'marketing')
            @include('admin.settings.tabs.marketing')
        @elseif($tab === 'branding')
            @include('admin.settings.tabs.branding')
        @elseif($tab === 'home')
            @include('admin.settings.tabs.home')
        @elseif($tab === 'about')
            @include('admin.settings.tabs.about')
        @elseif($tab === 'contact')
            @include('admin.settings.tabs.contact')
        @elseif($tab === 'shipping')
            @include('admin.settings.tabs.shipping')
        @elseif($tab === 'seo')
            @include('admin.settings.tabs.seo')
        @endif
    </div>
</div>
@endsection
