<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * The referenced record (a model using the Approvable trait) must be
 * approved before another activity can use it. A missing record passes, so
 * pair this with an `exists` rule.
 */
class Approved implements ValidationRule
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function __construct(private readonly string $modelClass) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $record = $this->modelClass::query()->find($value);

        if ($record && ! $record->isApproved()) {
            $fail(__('The selected :attribute is not approved yet. Approve it first.'));
        }
    }
}
