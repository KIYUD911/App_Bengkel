<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    public function render()
    {
        return view('livewire.customer.customer-list');
    }
}
