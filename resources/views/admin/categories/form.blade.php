@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.categories.index'))
@section('breadcrumb_parent_label', 'Categories')

@section('title', $category->exists ? 'Edit Category' : 'Add Category')
@section('heading', $category->exists ? 'Edit Category' : 'Add Category')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="w-full max-w-xl space-y-4 card p-4 sm:p-6">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="input-field">
    </div>

    @include('admin.partials.image-upload', ['current' => $category->image, 'label' => 'Category Image'])

    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="input-field !rounded-2xl">{{ old('description', $category->description) }}</textarea>
    </div>
    <button type="submit" class="btn-primary">Save</button>
</form>
@endsection
