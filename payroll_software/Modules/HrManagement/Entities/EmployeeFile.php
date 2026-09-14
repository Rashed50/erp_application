<?php

namespace Modules\HrManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_auto_id',
        'pasfort_photo',
        'profile_photo',
        'akama_photo',
        'medical_report',
        'appoint_letter',
        'covid_certificate',
        'blood_group_paper',
        'educational_papers',
        'ajeer_file',
        'signature_file',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee that owns these files.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_auto_id');
    }
    /**
     * Get the full URL for a specific file.
     */
    public function getFileUrl($fileColumn)
    {
         if ($this->$fileColumn && Storage::disk('s3')->exists($this->$fileColumn)) {
            // Get temporary URL (valid for 60 minutes)
            return Storage::disk('s3')->temporaryUrl(
                $this->$fileColumn,
                now()->addMinutes(60)
            );

            // Or if you want a permanent public URL (if bucket is public):
            // return Storage::disk('s3')->url($this->$fileColumn);
        }
        return null;

        // if ($this->$fileColumn && Storage::disk('public')->exists($this->$fileColumn)) {
        //     return Storage::url($this->$fileColumn);
        // }
        // return null;
    }

    /**
     * Get pasfort photo URL.
     */
    public function getPasfortPhotoUrlAttribute()
    {
        return $this->getFileUrl('pasfort_photo');
    }

    /**
     * Get profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->getFileUrl('profile_photo');
    }

    /**
     * Get akama photo URL.
     */
    public function getAkamaPhotoUrlAttribute()
    {
        return $this->getFileUrl('akama_photo');
    }

    /**
     * Get medical report URL.
     */
    public function getMedicalReportUrlAttribute()
    {
        return $this->getFileUrl('medical_report');
    }

    /**
     * Get employee appoint letter URL.
     */
    public function getEmployeeAppointLatterUrlAttribute()
    {
        return $this->getFileUrl('employee_appoint_latter');
    }

    /**
     * Get COVID certificate URL.
     */
    public function getCovidCertificateUrlAttribute()
    {
        return $this->getFileUrl('covid_certificate');
    }

    /**
     * Get blood group paper URL.
     */
    public function getBloodGroupPaperUrlAttribute()
    {
        return $this->getFileUrl('blood_group_paper');
    }

    /**
     * Get educational papers URL.
     */
    public function getEducationalPapersUrlAttribute()
    {
        return $this->getFileUrl('educational_papers');
    }

    /**
     * Get ajeer file URL.
     */
    public function getAjeerFileUrlAttribute()
    {
        return $this->getFileUrl('ajeer_file');
    }

    /**
     * Get signature file URL.
     */
    public function getSignatureFileUrlAttribute()
    {
        return $this->getFileUrl('signature_file');
    }

    /**
     * Scope a query to only include files for a specific employee.
     */
    public function scopeForEmployee($query, $empAutoId)
    {
        return $query->where('emp_auto_id', $empAutoId);
    }
}
