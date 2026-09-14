<?php
// app/Models/ResponsibleEmployee.php

namespace Modules\HrManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class ResponsibleEmployee extends Model
{
    // protected $table = 'responsible_employees'; 

    public static function getEmployees()
    {
        return [
            [
                'emp_auto_id'   => 4033,
                'employee_name' => 'SAYED ALI KHAN DELU',
                'employee_id'   => 2001
            ],
            [
                'emp_auto_id'   => 4034,
                'employee_name' => 'HAFIJUL SHAIKH RASID',
                'employee_id'   => 2002
            ],
            [
                'emp_auto_id'   => 4035,
                'employee_name' => 'MD OMAR FARUK MOTAJ UDDIN',
                'employee_id'   => 2003
            ],
            [
                'emp_auto_id'   => 4036,
                'employee_name' => 'JOYNAL KHAN ASRAF KHAN',
                'employee_id'   => 2004
            ],
            [
                'emp_auto_id'   => 4037,
                'employee_name' => 'ADNAN ASGHAR MUHAMMAD ASGHAR',
                'employee_id'   => 2005
            ]
        ];
    }

    public static function findById($employeeId)
    {
        $employees = self::getEmployees();

        foreach ($employees as $employee) {
            if ($employee['employee_id'] == $employeeId) {
                return (object)$employee;
            }
        }

        return null;
    }


    public static function findByAutoId($empAutoId)
    {
        $employees = self::getEmployees();

        foreach ($employees as $employee) {
            if ($employee['emp_auto_id'] == $empAutoId) {
                return (object)$employee;
            }
        }

        return null;
    }
}
