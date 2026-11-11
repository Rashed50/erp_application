<?php

namespace App\Http\Requests\Hr;

class UpdateSalaryDetailRequest extends StoreSalaryDetailRequest
{
    /**
     * The revision being edited belongs to its own employee rather than one
     * named in the URL.
     */
    protected function employeeId(): ?int
    {
        return $this->route('salary_detail')?->employee_id;
    }
}
