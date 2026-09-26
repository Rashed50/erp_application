<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreEmployeeFileRequest;
use App\Http\Resources\EmployeeFileResource;
use App\Http\Responses\ApiResponse;
use App\Models\Employee;
use App\Models\EmployeeFile;
use App\Services\EmployeeFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeFileController extends Controller
{
    public function __construct(private readonly EmployeeFileService $fileService) {}

    public function store(StoreEmployeeFileRequest $request, Employee $employee): JsonResponse
    {
        $file = $this->fileService->store(
            $employee,
            $request->file('file'),
            $request->validated('document_type'),
            $request->validated('title'),
        );

        return ApiResponse::success(new EmployeeFileResource($file), 'Document uploaded successfully.', 201);
    }

    public function download(EmployeeFile $employeeFile): StreamedResponse|JsonResponse
    {
        if (! Storage::disk(EmployeeFile::DISK)->exists($employeeFile->file_path)) {
            return ApiResponse::error('The file could not be found.', 404);
        }

        return Storage::disk(EmployeeFile::DISK)->download($employeeFile->file_path, $employeeFile->file_name);
    }

    public function destroy(EmployeeFile $employeeFile): JsonResponse
    {
        $this->fileService->delete($employeeFile);

        return ApiResponse::success(message: 'Document deleted successfully.');
    }
}
