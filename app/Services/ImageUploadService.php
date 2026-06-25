<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function storeProductImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/products', $oldPath);
    }

    public function storeCategoryImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/categories', $oldPath);
    }

    public function storeProductVideo(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'videos/products', $oldPath, ['mp4', 'webm', 'mov']);
    }

    public function storeHeroImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/hero', $oldPath);
    }

    public function storePromoBannerImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/promo-banners', $oldPath);
    }

    public function storeHeroVideo(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'videos/hero', $oldPath, ['mp4', 'webm', 'mov']);
    }

    public function storePublicImage(UploadedFile $file, string $directory, string $basename): string
    {
        $publicDir = public_path($directory);

        if (! File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }

        if (! is_writable($publicDir)) {
            throw new \RuntimeException("Upload directory is not writable: {$directory}. Please contact the server administrator.");
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = $basename.'-'.time().'.'.$extension;
        $file->move($publicDir, $filename);

        return $directory.'/'.$filename;
    }

    public function storeFounderImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/founder', $oldPath);
    }

    public function storeFounderIllustration(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->store($file, 'images/founder', $oldPath, ['svg', 'png', 'jpg', 'jpeg', 'webp']);
    }

    protected function store(UploadedFile $file, string $directory, ?string $oldPath = null, ?array $allowedExtensions = null): string
    {
        $publicDir = public_path($directory);

        if (! File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if ($allowedExtensions && ! in_array($extension, $allowedExtensions, true)) {
            throw new \InvalidArgumentException('Unsupported file type.');
        }

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = ($filename ?: 'file').'-'.time().'.'.$extension;

        $file->move($publicDir, $filename);

        $path = $directory.'/'.$filename;

        if ($oldPath && $this->isManagedUpload($oldPath) && $oldPath !== $path) {
            $this->delete($oldPath);
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (! $path || ! $this->isManagedUpload($path)) {
            return;
        }

        $fullPath = public_path($path);

        if (File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function isManagedUpload(string $path): bool
    {
        return str_starts_with($path, 'images/products/')
            || str_starts_with($path, 'images/categories/')
            || str_starts_with($path, 'images/hero/')
            || str_starts_with($path, 'images/promo-banners/')
            || str_starts_with($path, 'images/founder/')
            || str_starts_with($path, 'images/')
            || str_starts_with($path, 'videos/products/')
            || str_starts_with($path, 'videos/hero/');
    }
}
