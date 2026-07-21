<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\Attributes\Layout;

// Cancel WO dihandle langsung di WorkOrderDetail via modal.
// Class ini disimpan agar route tidak error.
#[Layout('layouts.app')]
class WorkOrderCancel extends Component
{
    public function render()
    {
        return view('livewire.work-order.work-order-cancel');
    }
}
