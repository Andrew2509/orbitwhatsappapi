<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Convert an uploaded file to a Base64 string
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function fileToBase64($file): string
    {
        $contents = file_get_contents($file->getRealPath());
        $mime = $file->getMimeType();
        $base64 = base64_encode($contents);
        
        return "data:{$mime};base64,{$base64}";
    }

    /**
     * Get image source for display
     * Handles both Base64 and standard URLs/paths
     *
     * @param string|null $image
     * @param string $default (placeholder image URL)
     * @return string
     */
    public static function display($image, $default = '/Image/user-placeholder.png'): string
    {
        if (empty($image)) {
            return asset($default);
        }

        // If it's already a full URL or Base64, return as is
        if (str_starts_with($image, 'http') || str_starts_with($image, 'data:image')) {
            return $image;
        }

        // Fallback for old storage paths
        if (Storage::disk('public')->exists($image)) {
            return asset('storage/' . $image);
        }

        return asset($default);
    }
}
