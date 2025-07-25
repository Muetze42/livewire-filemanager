<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Contracts\View\View as ViewInterface;
use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Folder;

class LivewireFilemanagerFolderPanelComponent extends Component
{
    public $folder;

    #[On('load-folder')]
    public function loadFolder(int $folder_id): void
    {
        $this->folder = Folder::find($folder_id);
    }

    #[On('reset-folder')]
    public function resetFolder(): void
    {
        $this->folder = null;
    }

    public function renameFolder(): void
    {
        $this->dispatch('rename-folder', folder: $this->folder);
    }

    public function render(): ViewInterface
    {
        return view('livewire-filemanager::livewire.folder-panel');
    }
}
