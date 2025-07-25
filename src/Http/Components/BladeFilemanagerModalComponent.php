<?php

namespace LivewireFilemanager\Filemanager\Http\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BladeFilemanagerModalComponent extends Component
{
    public function render(): Factory|View
    {
        return view('livewire-filemanager::components.livewire-filemanager-modal');
    }
}
