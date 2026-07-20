<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class WorkOrderCreate extends Component
{
    public function render()
    {
        return view('livewire.work-order.work-order-create');
    }
}
