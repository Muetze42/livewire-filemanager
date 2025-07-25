<?php

namespace LivewireFilemanager\Filemanager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LivewireFilemanager\Filemanager\Models\Media as FilemanagerMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property string|null $name
 * @property string|null $slug
 * @property int|null $parent_id
 * @property-read int|null $children_count
 * @property-read ?\Illuminate\Support\Carbon $created_at
 * @property-read ?\Illuminate\Support\Carbon $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<Folder>
 */
class Folder extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['children'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    public $registerMediaConversionsUsingModelInstance = true;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($folder) {
            if ($folder->isHomeFolder()) {
                return false;
            }
        });

        static::creating(static function ($folder) {
            if (! config('livewire-fileuploader.acl_enabled')) {
                return;
            }

            $user = auth()->getUser();

            if ($user) {
                $folder->user_id = $user->id;
            }
        });
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (! config('livewire-fileuploader.acl_enabled')) {
                return;
            }

            $user = auth()->getUser();

            if ($user) {
                $builder->where(
                    'user_id',
                    $user->id
                );
            }
        });
    }

    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    public function isHomeFolder(): bool
    {
        return is_null($this->parent_id);
    }

    public function parentWithoutRootFolder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id')
            ->whereNotNull('parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function elements(): string
    {
        return trans_choice(
            'livewire-filemanager::filemanager.elements',
            $this->children_count + $this->getMedia('medialibrary')->count(),
            ['value' => $this->children_count + $this->getMedia('medialibrary')->count()]
        );
    }

    /**
     * Some media conversions for all models
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->format('webp')->width(100)->performOnCollections('medialibrary');
    }

    public function getMediaModel(): string
    {
        return FilemanagerMedia::class;
    }
}
