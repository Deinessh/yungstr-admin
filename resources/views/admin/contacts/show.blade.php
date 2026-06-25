@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.contacts.index'))
@section('breadcrumb_parent_label', 'Contact Forms')

@section('title', 'Contact Message')
@section('heading', $submission->subject)

@section('content')
<div class="max-w-2xl card p-4 sm:p-6 space-y-4">
    <div class="text-sm text-gray-500">{{ $submission->created_at->format('M d, Y h:i A') }}</div>
    <p><strong>From:</strong> {{ $submission->name }} ({{ $submission->email }})</p>
    @if($submission->phone)<p><strong>Phone:</strong> {{ $submission->phone }}</p>@endif
    <div class="border-t border-gray-100 pt-4 whitespace-pre-line">{{ $submission->message }}</div>
    <form action="{{ route('admin.contacts.destroy', $submission) }}" method="POST" onsubmit="return confirm('Delete message?')">
        @csrf @method('DELETE')
        <button class="text-red-600 hover:underline text-sm">Delete Message</button>
    </form>
</div>
@endsection
