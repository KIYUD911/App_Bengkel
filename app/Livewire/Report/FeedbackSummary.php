<?php

namespace App\Livewire\Report;

use App\Models\CustomerFeedback;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class FeedbackSummary extends Component
{
    public string $dateFrom = '';
    public string $dateTo   = '';

    #[Computed]
    public function stats(): array
    {
        $query = CustomerFeedback::with(['customer', 'workOrder'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        $all     = $query->get();
        $total   = $all->count();
        $average = $total > 0 ? round($all->avg('rating'), 2) : 0;

        // Distribusi per rating
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $all->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'pct'   => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return [
            'total'        => $total,
            'average'      => $average,
            'distribution' => $distribution,
            'recent'       => $all->sortByDesc('created_at')->take(10)->values(),
        ];
    }

    public function render()
    {
        return view('livewire.report.feedback-summary')
            ->title('Ringkasan Feedback');
    }
}
