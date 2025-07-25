<?php

namespace LivewireFilemanager\Filemanager\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): true
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|string|list<ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug(trim($value));
                    $existingFolder = \LivewireFilemanager\Filemanager\Models\Folder::where('slug', $slug)
                        ->where('parent_id', $this->parent_id)
                        ->first();
                    if ($existingFolder) {
                        $fail('A folder with this name already exists.');
                    }
                },
            ],
            'parent_id' => 'nullable|exists:folders,id',
        ];
    }
}
