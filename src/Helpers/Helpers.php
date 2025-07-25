<?php

/**
 * Trims a string to a specified maximum length.
 * If the string length exceeds the specified maximum length, the middle part of the string
 * is replaced with dots ('....') to fit within the allowed length, keeping the first part
 * and the last 4 characters of the string.
 *
 * @param string  $string    The string to be trimmed.
 * @param int     $maxLength The maximum allowed length.
 *
 * @return string The trimmed string.
 */

namespace LivewireFilemanager\Filemanager\Helpers;

use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;

/*
|--------------------------------------------------------------------------
| TrimString
|--------------------------------------------------------------------------
|
*/

if (! function_exists('trimString')) {
    /**
     * Trims a string to a specified maximum length, appending dots and preserving the end of the string.
     *
     * @param string  $string    The input string to be trimmed.
     * @param int     $maxLength The maximum allowed length of the returned string.
     *
     * @return string
     */
    function trimString(string $string, int $maxLength): string
    {
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
    /**
     * Determines the file type based on a given MIME type.
     *
     * @param string|null  $mimeType The MIME type to evaluate. Can be null.
     *
     * @return string|null
     */
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
    /**
     * Constructs and returns the full path of a media file, including its hierarchy within associated folders.
     *
     * @param Media  $media The Media object for which the full path is to be generated.
     *
     * @return string
     */
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
    /**
     * Builds a full folder path string recursively based on the folder ID.
     *
     * @param int  $folderId The ID of the folder to build the path for.
     *
     * @return string
     */
    function buildFolderPath(int $folderId): string
    {
        $folder = Folder::find($folderId);

        if ($folder && $folder->parentWithoutRootFolder) {
            return buildFolderPath($folder->parentWithoutRootFolder->id).'/'.$folder->slug;
        }

        return $folder ? $folder->slug : '';
    }
}
