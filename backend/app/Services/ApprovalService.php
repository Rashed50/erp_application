<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApprovalService
{
    /**
     * @template TModel of Model
     *
     * @param  TModel  $model  A model using the Approvable trait.
     * @return TModel
     */
    public function approve(Model $model): Model
    {
        $model->forceFill([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ])->save();

        return $model;
    }

    /**
     * Refuse an activity on a record that has not been approved yet, e.g. a
     * payment against a pending sale.
     *
     * @param  Model  $model  A model using the Approvable trait.
     *
     * @throws ValidationException
     */
    public function ensureApproved(Model $model, string $field = 'approval'): void
    {
        if (! $model->isApproved()) {
            throw ValidationException::withMessages([
                $field => __('This record is not approved yet. Approve it before doing any activity with it.'),
            ]);
        }
    }
}
