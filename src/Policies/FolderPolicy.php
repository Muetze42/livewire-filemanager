<?php

namespace LivewireFilemanager\Filemanager\Policies;

use Illuminate\Foundation\Auth\User;
use LivewireFilemanager\Filemanager\Models\Folder;

class FolderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Folder $folder): bool
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $folder->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Folder $folder): bool
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $folder->user_id === $user->id;
    }

    public function delete(User $user, Folder $folder): bool
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        if ($folder->isHomeFolder()) {
            return false;
        }

        return $folder->user_id === $user->id;
    }

    public function restore(User $user, Folder $folder): bool
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $folder->user_id === $user->id;
    }

    public function forceDelete(User $user, Folder $folder): bool
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $folder->user_id === $user->id;
    }
}
