<?php

use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;

/*
|--------------------------------------------------------------------------
| TrimString
|--------------------------------------------------------------------------
|
*/

if (! function_exists('trimString')) {
    function trimString($string, $maxLength)
    {
        $extension = pathinfo($string, PATHINFO_EXTENSION);
        $baseLength = $maxLength - 8; // 4 for the dots and 4 for the last part of the filename

        if (strlen($string) <= $maxLength) {
            return $string;
        }

        $trimmedBase = substr($string, 0, $baseLength);
        $end = substr($string, -4); // Get last 4 characters

        return $trimmedBase.'....'.$end;
    }
}

/*
|--------------------------------------------------------------------------
| Get the file mime type
|--------------------------------------------------------------------------
|
*/

if (! function_exists('getFileType')) {
    function getFileType(?string $mimeType): ?string
    {
        if (! $mimeType) {
            return null;
        }

        return match ($mimeType) {
            'application/pdf' => 'pdf',
            'application/zip', 'application/x-zip-compressed' => 'zip',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'video/webm', 'video/ogg', 'video/mp4' => 'video',
            'audio/ogg', 'audio/wav', 'audio/mpeg' => 'audio',
            default => 'file',
        };
    }
}

/*
|--------------------------------------------------------------------------
| Build the folder path
|--------------------------------------------------------------------------
|
*/

if (! function_exists('getMediaFullPath')) {
    function getMediaFullPath(Media $media): string
    {
        $folder = Folder::where('id', $media->model_id)->first();

        // Initialize the path with the media file name
        $path = [$media->file_name];

        // Traverse up the folder hierarchy
        while ($folder) {
            array_unshift($path, $folder->slug);

            $folder = $folder->parentWithoutRootFolder;
        }

        // Return the full path as a string
        return config('app.url').'/'.implode('/', $path);
    }
}

/*
|--------------------------------------------------------------------------
| Build the folder path
|--------------------------------------------------------------------------
|
*/

if (! function_exists('buildFolderPath')) {
    function buildFolderPath($folderId)
    {
        $folder = Folder::find($folderId);

        if ($folder && $folder->parentWithoutRootFolder) {
            return buildFolderPath($folder->parentWithoutRootFolder->id).'/'.$folder->slug;
        }

        return $folder ? $folder->slug : '';
    }
}
