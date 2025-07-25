<?php

namespace LivewireFilemanager\Filemanager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \LivewireFilemanager\Filemanager\Models\Folder
 */
class FolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'is_home_folder' => $this->isHomeFolder(),
            'elements_count' => $this->children_count + $this->getMedia('medialibrary')->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => static::collection($this->whenLoaded('children')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
