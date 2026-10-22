<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Keeps a model's `attachment` column (a path on the public disk) in sync with
 * an uploaded invoice/voucher file.
 */
class AttachmentService
{
    /**
     * Request keys that drive the attachment and must not be filled onto the model directly.
     */
    public const FIELDS = ['attachment', 'remove_attachment'];

    /**
     * Store a newly uploaded file, or clear the attachment when `remove_attachment`
     * is set, on the (unsaved) model. The replaced file is deleted only once the
     * surrounding transaction commits, so a rolled-back save keeps it.
     *
     * @param  array{attachment?: ?UploadedFile, remove_attachment?: bool}  $data
     */
    public function sync(Model $model, array $data, string $directory): void
    {
        $previousAttachment = $model->attachment;

        if (isset($data['attachment'])) {
            $model->attachment = $data['attachment']->store($directory, 'public');
        } elseif ($data['remove_attachment'] ?? false) {
            $model->attachment = null;
        }

        if ($previousAttachment && $previousAttachment !== $model->attachment) {
            DB::afterCommit(fn () => Storage::disk('public')->delete($previousAttachment));
        }
    }
}
