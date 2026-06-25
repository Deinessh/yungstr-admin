@extends('admin.layout')

@section('title', 'Products')
@section('heading', 'Products')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-sm text-gray-500">Manage catalog and homepage best sellers.</p>
    <a href="{{ route('admin.products.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Add Product</a>
</div>

<div class="card overflow-x-auto -mx-4 sm:mx-0 rounded-none sm:rounded-2xl border-x-0 sm:border-x">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="text-left">
            <tr>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Featured</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($products as $product)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-semibold">{{ $product->name }}</div>
                    <div class="text-xs text-gray-500">{{ $product->category?->name }}</div>
                </td>
                <td class="px-4 py-3">₹{{ number_format($product->price, 0) }}</td>
                <td class="px-4 py-3">{{ $product->stock === null ? 'Unlimited' : $product->stock }}</td>
                <td class="px-4 py-3">
                    @if($product->is_featured)
                        <span class="text-xs bg-brand-green-soft text-brand-green px-2 py-1 rounded-full">Yes ({{ $product->featured_sort }})</span>
                    @else
                        <span class="text-gray-400">No</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($product->is_coming_soon)
                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded-full font-semibold">Coming Soon</span>
                    @elseif($product->is_active)
                        <span class="text-xs text-brand-green font-medium">Active</span>
                    @else
                        <span class="text-xs text-gray-400">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-orange hover:underline mr-3">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
