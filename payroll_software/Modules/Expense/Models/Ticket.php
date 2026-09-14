<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';
    protected $primaryKey = 'ticket_auto_id';

    protected $fillable = ['ticket_for', 'emp_auto_id', 'ticket_type', 'paid_by', 'ticket_number', 'confirm_date', 'qty', 'unit_price', 'total_price', 'remarks', 'reference_by', 'attachment', 'is_approved', 'created_by', 'updated_by'];


    // Define the relationship with the EmployeeInfo model
    public function employeeInfo()
    {
        return $this->belongsTo(\App\Models\EmployeeInfo::class, 'emp_auto_id', 'emp_auto_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by', 'id');
    }



    // Relationship to the User who created the ticket
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    // Relationship to the User who last updated the ticket
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

}