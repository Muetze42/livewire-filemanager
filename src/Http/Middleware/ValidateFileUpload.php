<?php

namespace LivewireFilemanager\Filemanager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('files')) {
            $this->validateFiles($request);
        }

        return $next($request);
    }

    private function validateFiles(Request $request)
    {
        $maxSize = config('livewire-fileuploader.api.max_file_size', 10240);
        $allowedExtensions = config('livewire-fileuploader.api.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf', 'txt']);

        $files = $request->file('files');
        if (! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $validator = Validator::make([
                'file' => $file,
            ], [
                'file' => [
                    'required',
                    'file',
                    'max:'.$maxSize,
                    'mimes:'.implode(',', $allowedExtensions),
                ],
            ]);

            if ($validator->fails()) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
            }

            $this->validateFileName($file->getClientOriginalName());
            $this->validateFilePath($file->getRealPath());
        }
    }

    private function validateFileName(string $fileName): void
    {
        if (preg_match('/[\\\\\/\:\*\?\"\<\>\|]/', $fileName)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid file name characters');
        }

        if (str_contains($fileName, '..')) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Path traversal detected in file name');
        }
    }

    private function validateFilePath(string $filePath): void
    {
        $realPath = realpath($filePath);
        $allowedPath = realpath(sys_get_temp_dir());

        if ($realPath === false || ! str_starts_with($realPath, $allowedPath)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid file path');
        }
    }
}
