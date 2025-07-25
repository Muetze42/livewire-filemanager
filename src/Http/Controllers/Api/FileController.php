<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use LivewireFilemanager\Filemanager\Http\Requests\Api\UpdateFileRequest;
use LivewireFilemanager\Filemanager\Http\Resources\MediaResource;
use LivewireFilemanager\Filemanager\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $files = Media::query()
            ->when($request->folder_id, function ($query, $folderId) {
                return $query->where('model_id', $folderId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->get();

        return MediaResource::collection($files);
    }

    /**
     * @param \LivewireFilemanager\Filemanager\Models\Media  $file
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Media $file): JsonResponse|BinaryFileResponse
    {
        $this->authorize('view', $file);

        $filePath = $file->getPath();

        if (! file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $fileMimeType = mime_content_type($filePath);

        if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'])) {
            return response()->file($filePath);
        }

        return response()->download($filePath, $file->file_name);
    }

    public function update(UpdateFileRequest $request, Media $file): MediaResource
    {
        $this->authorize('update', $file);

        $file->update([
            'name' => $request->name,
        ]);

        return new MediaResource($file);
    }

    public function destroy(Media $file): JsonResponse
    {
        $this->authorize('delete', $file);

        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
