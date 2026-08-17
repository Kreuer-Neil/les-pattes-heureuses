<?php

namespace App\Http\Requests\Settings;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * A volunteer may only change their own email/avatar — their name is admin-managed
     * (see the users management page). An admin can change their own name too.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => 'nullable|file|image|extensions:jpg,jpeg,png,webp,gif|max:5120',
        ];

        if ($this->user()->role() === Roles::Admin->value) {
            $rules['name'] = ['sometimes', 'string', 'max:255'];
        }

        return $rules;
    }
}
