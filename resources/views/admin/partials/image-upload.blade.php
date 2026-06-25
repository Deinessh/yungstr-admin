@props(['current' => null, 'label' => 'Image'])

@php
$currentUrl = $current ? asset($current) : null;
@endphp

<div class="md:col-span-2" x-data="{ preview: @js($currentUrl) }">
    <label class="block text-sm font-medium mb-2">{{ $label }}</label>

    <div class="mb-3">
        <p class="text-xs text-gray-500 mb-2">{{ $currentUrl ? 'Current image' : 'Preview' }}</p>
        <div class="w-40 h-40 rounded-xl border border-amber-100 bg-amber-50/40 flex items-center justify-center overflow-hidden">
            <img x-show="preview" :src="preview" alt="Image preview" class="max-h-full max-w-full object-contain">
            <span x-show="!preview" class="text-xs text-gray-400 text-center px-4">No image uploaded yet</span>
        </div>
    </div>

    <input
        type="file"
        name="image"
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-green-soft file:text-brand-green hover:file:bg-amber-100"
        @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : preview"
    >
    <p class="text-xs text-gray-500 mt-2">Recommended: <strong>1200×1200 px</strong> (square), JPG/PNG/WebP, max 5 MB. Use a clean product photo on white or cream background.</p>
    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
