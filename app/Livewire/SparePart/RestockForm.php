<?php

namespace App\Livewire\SparePart;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RestockForm extends Component
{
    public function render()
    {
        return view('livewire.spare-part.restock-form');
    }
}
