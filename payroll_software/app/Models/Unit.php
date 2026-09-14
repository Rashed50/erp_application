<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];
    static function current_branch(?int $branch_id = null)
    {
        return self::query()->where(function ($q) use ($branch_id) {
            $q->where('branch_office_id', $branch_id ?: auth()->user()->id)
                ->orWhereNull('branch_office_id');
        });
    }
}
