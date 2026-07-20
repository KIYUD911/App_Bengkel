<?php

namespace App\Livewire\AuditLog;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AuditLogList extends Component
{
    public function render()
    {
        return view('livewire.audit-log.audit-log-list');
    }
}
