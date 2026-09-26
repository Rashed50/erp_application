<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeFileService
{
    public function store(Employee $employee, UploadedFile $file, string $documentType, ?string $title = null): EmployeeFile
    {
        return $employee->files()->create([
            'document_type' => $documentType,
            'title' => $title,
            'file_path' => $file->store("employees/{$employee->id}", EmployeeFile::DISK),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function delete(EmployeeFile $file): void
    {
        Storage::disk(EmployeeFile::DISK)->delete($file->file_path);
        $file->delete();
    }
}
