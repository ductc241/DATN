<?php

namespace App\Rules;

use App\Models\Supplier;
use Illuminate\Contracts\Validation\Rule;

class CheckSupplierRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Supplier::query()->where('id',$value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Nhà cung cấp không tồn tại';
    }
}
