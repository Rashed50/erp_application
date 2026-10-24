<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The single pipeline every uploaded file goes through. Which disk holds the
 * files is decided only here, from `filesystems.uploads` (UPLOADS_PUBLIC_DISK /
 * UPLOADS_PRIVATE_DISK), so moving storage (e.g. to S3) is a config change.
 *
 * Public files (logos, profile photos, invoice attachments) get a URL; private
 * files (employee documents) are only streamed through authorized controllers.
 */
class FileStorageService
{
    /**
     * Store an upload under the given directory and return its path on the disk.
     */
    public function store(UploadedFile $file, string $directory, bool $isPrivate = false): string
    {
        return $file->store($directory, $this->diskName($isPrivate));
    }

    /**
     * Store a new upload or clear the current file, returning the path to keep.
     * The replaced file is deleted once the surrounding transaction commits (or
     * right away outside one), so a rolled-back save keeps it.
     */
    public function replace(?string $currentPath, ?UploadedFile $file, bool $shouldRemove, string $directory, bool $isPrivate = false): ?string
    {
        $newPath = match (true) {
            $file !== null => $this->store($file, $directory, $isPrivate),
            $shouldRemove => null,
            default => $currentPath,
        };

        if ($currentPath && $currentPath !== $newPath) {
            DB::afterCommit(fn () => $this->delete($currentPath, $isPrivate));
        }

        return $newPath;
    }

    public function delete(?string $path, bool $isPrivate = false): void
    {
        if ($path) {
            $this->disk($isPrivate)->delete($path);
        }
    }

    public function exists(?string $path, bool $isPrivate = false): bool
    {
        return $path !== null && $this->disk($isPrivate)->exists($path);
    }

    /**
     * Public URL of a stored public file.
     */
    public function url(?string $path): ?string
    {
        return $path ? $this->disk()->url($path) : null;
    }

    public function download(string $path, ?string $fileName = null, bool $isPrivate = false): StreamedResponse
    {
        return $this->disk($isPrivate)->download($path, $fileName);
    }

    public function diskName(bool $isPrivate = false): string
    {
        return config($isPrivate ? 'filesystems.uploads.private_disk' : 'filesystems.uploads.public_disk');
    }

    private function disk(bool $isPrivate = false): Filesystem
    {
        return Storage::disk($this->diskName($isPrivate));
    }
}
