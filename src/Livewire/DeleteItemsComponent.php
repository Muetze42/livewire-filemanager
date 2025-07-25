<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Contracts\View\View as ViewInterface;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteItemsComponent extends Component
{
    public $files;

    public $folders;

    #[On('delete-items')]
    public function deleteItems(array $folders, array $files): void
    {
        $this->folders = $folders;

        $this->files = $files;
    }

    public function delete(): void
    {
        foreach ($this->files as $file) {
            Media::find($file)->delete();
        }

        foreach ($this->folders as $folder) {
            Folder::find($folder)->delete();
        }

        $this->dispatch('reset-media');
    }

    public function render(): ViewInterface
    {
        return view('livewire-filemanager::livewire.delete-items');
    }
}
