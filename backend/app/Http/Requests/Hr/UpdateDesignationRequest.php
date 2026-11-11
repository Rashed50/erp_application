<?php

namespace App\Http\Requests\Hr;

/**
 * Same rules as creating; the unique name check ignores the designation
 * being edited.
 *
 * Authorization is enforced by the `permission:designations.update`
 * middleware on the route.
 */
class UpdateDesignationRequest extends StoreDesignationRequest {}
