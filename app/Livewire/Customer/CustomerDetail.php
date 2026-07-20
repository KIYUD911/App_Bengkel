<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerDetail extends Component
{
    public function render()
    {
        return view('livewire.customer.customer-detail');
    }
}
