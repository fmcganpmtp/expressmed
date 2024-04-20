<?php

namespace App\Rules;

use Auth;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class MatchOldPassword implements Rule
{
    public function passes($attribute, $value)
    {
        if (Auth::guard('user')->user()) {
            return Hash::check($value, Auth::guard('user')->user()->password);

        } elseif (Auth::guard('admin')->user()) {

            return Hash::check($value, Auth::guard('admin')->user()->password);
        }
    }

    public function message()
    {
        return ':attribute must match with old password.';
    }
}
