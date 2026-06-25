@extends('admin.layout')

@section('title', 'Testimonials')
@section('heading', 'Testimonials')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-brand-brown/70">Manage homepage customer testimonials</p>
    <a href="{{ route('admin.testimonials.create') }}" class="btn-primary text-sm">Add Testimonial</a>
</div>

<div class="card overflow-hidden">
    <table class="admin-table w-full text-sm">
        <thead>
            <tr>
                <th class="text-left">Customer</th>
                <th class="text-left hidden md:table-cell">Quote</th>
                <th>Rating</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testimonials as $testimonial)
            <tr>
                <td class="px-4 py-3">
                    <p class="font-semibold text-brand-brown">{{ $testimonial->name }}</p>
                    @if($testimonial->role)<p class="text-xs text-brand-brown/60">{{ $testimonial->role }}</p>@endif
                </td>
                <td class="px-4 py-3 hidden md:table-cell max-w-md truncate text-brand-brown/80">{{ $testimonial->quote }}</td>
                <td class="px-4 py-3 text-center">{{ $testimonial->rating }}/5</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $testimonial->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $testimonial->is_active ? 'Active' : 'Hidden' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="text-brand-orange hover:underline">Edit</a>
                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="inline" onsubmit="return confirm('Delete testimonial?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-brand-brown/50">No testimonials yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $testimonials->links() }}</div>
@endsection
