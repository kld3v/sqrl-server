<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use Libraries\Snipe\BanBuilder\CensorWords;
//use Snipe\BanBuilder\CensorWords;

class ValidUsername implements Rule
{
    protected $message = 'The :attribute is invalid.';

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */    
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            $this->message = 'The :attribute cannot be empty.';
            return false;
        }

        $censor = new CensorWords();
        $censoredResult = $censor->censorString($value);
        if ($censoredResult['orig'] != $censoredResult['clean']) {
            $this->message = 'The :attribute contains forbidden words.';
            return false;
        }

        if (User::where('username', $value)->exists()) {
            $this->message = 'The :attribute is unavailable.';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
