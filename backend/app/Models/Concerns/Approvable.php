<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Adds the approved_by / approved_at audit columns to a parent record.
 *
 * `approved_at` is the source of truth: records approved by the system (e.g.
 * the seeded chart of accounts) carry an approval time but no approver. A
 * record that is not approved cannot be used by any other activity, and it
 * falls back to pending whenever it is edited.
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
        return $this->approved_at !== null;
    }

    /**
     * Send the record back for approval (not saved here), e.g. after an edit.
     * Returns whether it had been approved before.
     */
    public function resetApproval(): bool
    {
        $wasApproved = $this->isApproved();

        $this->forceFill(['approved_by' => null, 'approved_at' => null]);

        return $wasApproved;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull($query->qualifyColumn('approved_at'));
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('approved_at'));
    }

    /**
     * Filter by approval state, or leave the query alone when null.
     */
    public function scopeApprovalStatus(Builder $query, ?bool $approved): Builder
    {
        return match ($approved) {
            true => $query->approved(),
            false => $query->pendingApproval(),
            null => $query,
        };
    }
}
