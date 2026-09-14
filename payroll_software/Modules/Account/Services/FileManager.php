<?php

namespace Modules\Account\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileManager
{
    public static function upload(string $dir, $files = [])
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            // Use the original file name or generate a unique name
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $file->getClientOriginalExtension();

            // Ensure the directory exists
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            // Store the file using putFileAs
            $filePath = $file->storeAs($dir, $fileName, 'public');
            $uploadedFiles[] = $filePath; // Store the full path of the uploaded file
        }

        return $uploadedFiles;
    }

    public static function singleUpload(string $dir, $files = [])
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            $filePath = $file->storeAs($dir, $fileName, 'public');
            $uploadedFiles[] = $filePath;
        }

        return count($uploadedFiles) === 1 ? $uploadedFiles[0] : $uploadedFiles;
    }

}
