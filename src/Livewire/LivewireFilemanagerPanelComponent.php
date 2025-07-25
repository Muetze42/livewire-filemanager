<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Contracts\View\View as ViewInterface;
use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Media;

class LivewireFilemanagerPanelComponent extends Component
{
    public $media;

    #[On('load-media')]
    public function loadMedia(int $media_id): void
    {
        $this->media = Media::find($media_id);
    }

    #[On('reset-media')]
    public function resetMedia(): void
    {
        $this->media = null;
    }

    public function renameFile(): void
    {
        $this->dispatch('rename-file', file: $this->media);
    }

    public function render(): ViewInterface
    {
        return view('livewire-filemanager::livewire.media-panel');
    }
}
