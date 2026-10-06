<?php

namespace App\Http\Requests\Hr;

/**
 * Same rules as creating; the unique name check ignores the department
 * being edited.
 *
 * Authorization is enforced by the `permission:departments.update`
 * middleware on the route.
 */
class UpdateDepartmentRequest extends StoreDepartmentRequest {}
