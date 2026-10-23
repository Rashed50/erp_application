<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Employee documents live on the private upload disk.
 */
class EmployeeFileService
{
    public function __construct(private readonly FileStorageService $fileStorage) {}

    public function store(Employee $employee, UploadedFile $file, string $documentType, ?string $title = null): EmployeeFile
    {
        return $employee->files()->create([
            'document_type' => $documentType,
            'title' => $title,
            'file_path' => $this->fileStorage->store($file, "employees/{$employee->id}", isPrivate: true),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function exists(EmployeeFile $file): bool
    {
        return $this->fileStorage->exists($file->file_path, isPrivate: true);
    }

    public function download(EmployeeFile $file): StreamedResponse
    {
        return $this->fileStorage->download($file->file_path, $file->file_name, isPrivate: true);
    }

    public function delete(EmployeeFile $file): void
    {
        $this->fileStorage->delete($file->file_path, isPrivate: true);
        $file->delete();
    }
}
