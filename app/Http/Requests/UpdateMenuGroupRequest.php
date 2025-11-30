<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|'. Rule::unique('menu_groups')->ignore($this->menu_group),
            'permission_name' => 'required|string|max:100',
            'icon' => 'required|string|max:50',
            'route' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
