<?php

namespace App\Livewire\WorkOrder;

use App\Models\WorkOrder;
use App\Services\CRMService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.app')]
class FeedbackForm extends Component
{
    #[Locked]
    public WorkOrder $workOrder;

    public int    $rating  = 0;    // 0 = belum pilih
    public int    $hover   = 0;    // untuk hover effect Alpine
    public string $comment = '';

    public bool $submitted = false;

    public function mount(WorkOrder $workOrder): void
    {
        $this->workOrder = $workOrder->load(['customer', 'feedback']);
    }

    public function setRating(int $value): void
    {
        $this->rating = $value;
    }

    public function submit(CRMService $crm): void
    {
        $this->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $crm->submitFeedback(
                $this->workOrder,
                $this->rating,
                $this->comment ?: null,
                auth()->user(),
            );

            $this->workOrder->load('feedback');
            $this->submitted = true;
            $this->dispatch('notify', type: 'success', message: 'Feedback berhasil disimpan! Terima kasih atas penilaiannya.');

        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.work-order.feedback-form')
            ->title('Feedback: ' . $this->workOrder->wo_number);
    }
}
