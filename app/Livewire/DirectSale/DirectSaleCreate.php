<?php

namespace App\Livewire\DirectSale;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DirectSaleCreate extends Component
{
    public function render()
    {
        return view('livewire.direct-sale.direct-sale-create');
    }
}
