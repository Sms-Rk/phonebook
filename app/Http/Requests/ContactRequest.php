<?php

// app/Http/Requests/ContactRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:contacts,email,' . $this->route('phone') . ',phone',
            'phone' => 'required|string|max:20|unique:contacts,phone,' . $this->route('phone') . ',phone',
        ];
    }
}
