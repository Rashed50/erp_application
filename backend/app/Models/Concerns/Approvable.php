<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Adds the approved_by / approved_at audit columns to a parent record.
 */
trait Approvable
{
    public function initializeApprovable(): void
    {
        $this->mergeCasts(['approved_at' => 'datetime']);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->approved_by !== null;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_by');
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->whereNull('approved_by');
    }
}
