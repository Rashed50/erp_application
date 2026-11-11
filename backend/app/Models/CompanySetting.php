<?php

namespace App\Models;

use Database\Factories\CompanySettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    /** @use HasFactory<CompanySettingFactory> */
    use HasFactory;

    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'address',
        'logo',
        'updated_by',
    ];

    /**
     * Get the application's single settings row, or an unsaved blank one.
     */
    public static function current(): self
    {
        return static::query()->oldest('id')->firstOrNew();
    }
}
