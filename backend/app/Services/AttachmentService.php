<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Keeps a model's `attachment` column (a path on the public upload disk) in
 * sync with an uploaded invoice/voucher file.
 */
class AttachmentService
{
    /**
     * Request keys that drive the attachment and must not be filled onto the model directly.
     */
    public const FIELDS = ['attachment', 'remove_attachment'];

    public function __construct(private readonly FileStorageService $fileStorage) {}

    /**
     * Store the attachment of a new record, if one was uploaded.
     */
    public function store(?UploadedFile $attachment, string $directory): ?string
    {
        return $attachment ? $this->fileStorage->store($attachment, $directory) : null;
    }

    /**
     * Store a newly uploaded file, or clear the attachment when `remove_attachment`
     * is set, on the (unsaved) model. The replaced file is deleted only once the
     * surrounding transaction commits, so a rolled-back save keeps it.
     *
     * @param  array{attachment?: ?UploadedFile, remove_attachment?: bool}  $data
     */
    public function sync(Model $model, array $data, string $directory): void
    {
        $model->attachment = $this->fileStorage->replace(
            $model->attachment,
            $data['attachment'] ?? null,
            $data['remove_attachment'] ?? false,
            $directory,
        );
    }
}
