<?php

namespace LivewireFilemanager\Filemanager\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadFilesRequest extends FormRequest
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
     * @return array<string, ValidationRule|string|list<ValidationRule|string>>
     */
    public function rules(): array
    {
        $maxSize = config('livewire-fileuploader.api.max_file_size', 10240);
        $allowedExtensions = config('livewire-fileuploader.api.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf', 'txt']);

        return [
            'files' => 'required|array|min:1',
            'files.*' => [
                'required',
                'file',
                'max:'.$maxSize,
                'mimes:'.implode(',', $allowedExtensions),
            ],
        ];
    }
}
