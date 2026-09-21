<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
}
