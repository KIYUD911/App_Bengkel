<?php

namespace App\Livewire\SparePart;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SparePartForm extends Component
{
    public function render()
    {
        return view('livewire.spare-part.spare-part-form');
    }
}
