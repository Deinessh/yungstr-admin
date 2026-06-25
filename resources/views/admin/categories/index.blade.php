@extends('admin.layout')

@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-end gap-3 mb-6">
    <a href="{{ route('admin.categories.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Add Category</a>
</div>
<div class="card overflow-x-auto -mx-4 sm:mx-0 rounded-none sm:rounded-2xl border-x-0 sm:border-x">
    <table class="w-full text-sm min-w-[480px]">
        <thead class="text-left">
            <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Products</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($categories as $category)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $category->name }}</td>
                <td class="px-4 py-3">{{ $category->products_count }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-orange hover:underline mr-3">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete category?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
