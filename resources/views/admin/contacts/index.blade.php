@extends('admin.layout')

@section('title', 'Contact Forms')
@section('heading', 'Contact Submissions')

@section('content')
<div class="card overflow-x-auto -mx-4 sm:mx-0 rounded-none sm:rounded-2xl border-x-0 sm:border-x">
    <table class="w-full text-sm min-w-[560px]">
        <thead class="text-left">
            <tr><th class="px-4 py-3">From</th><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Date</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($submissions as $submission)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-semibold">{{ $submission->name }}</div>
                    <div class="text-xs text-gray-500">{{ $submission->email }}</div>
                </td>
                <td class="px-4 py-3">{{ $submission->subject }} @unless($submission->is_read)<span class="text-xs text-brand-orange">New</span>@endunless</td>
                <td class="px-4 py-3">{{ $submission->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.contacts.show', $submission) }}" class="text-brand-orange hover:underline">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $submissions->links() }}</div>
@endsection
