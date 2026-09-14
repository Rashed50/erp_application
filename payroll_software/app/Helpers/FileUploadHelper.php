<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * Upload a file with proper handling
     *
     * @param UploadedFile|null $file
     * @param string|null $oldFilePath
     * @param string $destinationPath
     * @param int $maxSize MB
     * @param array $allowedMimeTypes
     * @return string|null
     * @throws \Exception
     */
    public static function uploadFile(
        ?UploadedFile $file,
        ?string $oldFilePath = null,
        string $destinationPath = 'uploads/',
        int $maxSize = 5,
        array $allowedMimeTypes = []
    ): ?string {
        // If no file provided, return null
        if (!$file || !$file->isValid()) {
            return null;
        }

        try {
            // Validate file size
            if ($file->getSize() > $maxSize * 1024 * 1024) {
                throw new \Exception("File size exceeds maximum allowed size of {$maxSize}MB");
            }

            // Validate MIME type
            if (!empty($allowedMimeTypes)) {
                $mimeType = $file->getMimeType();
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    throw new \Exception("File type not allowed. Allowed types: " . implode(', ', $allowedMimeTypes));
                }
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $uniqueName = $filename . '-' . time() . '.' . $extension;

            // Ensure destination path exists - using storage path
            $fullPath = storage_path('app/public/' . $destinationPath);
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            // Store the file using Storage facade
            $path = $file->storeAs('public/' . $destinationPath, $uniqueName);

            // Delete old file if exists
            if ($oldFilePath) {
                self::deleteFile($oldFilePath);
            }

            // Return path without 'public/' prefix for database storage
            return str_replace('public/', '', $path);
        } catch (\Exception $e) {
            throw new \Exception("File upload failed: " . $e->getMessage());
        }
    }

    /**
     * Delete a file if it exists
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function deleteFile(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        // Delete using Storage facade
        return Storage::delete('public/' . $filePath);
    }


 
    
    

    /**
     * Get file URL for public access
     *
     * @param string|null $filePath
     * @return string|null
     */
    public static function getFileUrl(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        // Generate URL using Storage facade
        return Storage::url($filePath);
    }
}