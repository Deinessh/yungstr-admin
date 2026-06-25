@extends('layouts.master')

@section('content')
<section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 lg:px-12 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-brand-dark mb-4">{{ $storeSettings['contact_heading'] }}</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $storeSettings['contact_subtitle'] }}</p>
    </div>
</section>

<div class="bg-cream py-16 px-4 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            @foreach([
                ['title' => 'Our Location', 'content' => $storeSettings['contact_address'], 'icon' => 'fas fa-map-marker-alt'],
                ['title' => 'Email Us', 'content' => $storeSettings['store_email'], 'icon' => 'fas fa-envelope'],
                ['title' => 'WhatsApp / Call', 'content' => $storeSettings['store_phone'].'<br>'.$storeSettings['contact_hours'], 'icon' => 'fab fa-whatsapp'],
            ] as $info)
            <div class="card p-8">
                <div class="flex items-start gap-4">
                    <div class="bg-brand-green-soft p-3 rounded-full text-brand-green shrink-0"><i class="{{ $info['icon'] }}"></i></div>
                    <div>
                        <h3 class="font-bold text-brand-dark text-lg mb-1">{{ $info['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{!! $info['content'] !!}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="lg:col-span-2">
            <div class="card p-8 md:p-12">
                <h2 class="text-2xl font-bold text-brand-dark mb-6">Send us a Message</h2>
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-xl">{{ session('success') }}</div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-brand-dark mb-2">Your Name</label>
                            <input type="text" id="name" name="name" required class="input-field" placeholder="John Doe" value="{{ old('name') }}">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-brand-dark mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required class="input-field" placeholder="john@example.com" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-brand-dark mb-2">Phone (optional)</label>
                        <input type="text" id="phone" name="phone" class="input-field" placeholder="+91 98765 43210" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-brand-dark mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" required class="input-field" placeholder="How can we help you?" value="{{ old('subject') }}">
                    </div>
                    <div class="mb-8">
                        <label for="message" class="block text-sm font-medium text-brand-dark mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" required class="input-field !rounded-2xl" placeholder="Write your message here...">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary px-8 py-4">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
