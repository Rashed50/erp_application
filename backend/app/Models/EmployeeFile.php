<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFile extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    public const DOCUMENT_TYPES = ['NID', 'Passport', 'CV', 'Joining Document', 'Certificate', 'Other'];

    /**
     * Private disk the documents are stored on.
     */
    public const DISK = 'local';

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
