<?php

namespace App\Livewire\Report;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RevenueReport extends Component
{
    public function render()
    {
        return view('livewire.report.revenue-report');
    }
}
