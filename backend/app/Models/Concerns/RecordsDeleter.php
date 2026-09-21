<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Records who soft deleted a record. Use alongside SoftDeletes.
 */
trait RecordsDeleter
{
    public static function bootRecordsDeleter(): void
    {
        static::deleting(function ($model): void {
            if (! $model->isForceDeleting() && Auth::id() !== null) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model): void {
            $model->deleted_by = null;
        });
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
