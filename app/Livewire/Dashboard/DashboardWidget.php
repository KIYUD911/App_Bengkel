<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use App\Models\WorkOrder;
use App\Models\DirectSale;
use App\Models\Customer;
use App\Models\SparePart;
use App\Models\StockMovement;

#[Layout('layouts.app')]
class DashboardWidget extends Component
{
    // ─── Owner Dashboard ─────────────────────────────────────
    #[Computed]
    public function todayRevenue(): float
    {
        $wo = WorkOrder::completed()
            ->whereDate('paid_at', today())
            ->sum('grand_total');

        $ds = DirectSale::whereDate('paid_at', today())->sum('grand_total');

        return (float) ($wo + $ds);
    }

    #[Computed]
    public function activeWoCount(): int
    {
        return WorkOrder::active()->count();
    }

    #[Computed]
    public function completedWoThisMonth(): int
    {
        return WorkOrder::completed()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->count();
    }

    #[Computed]
    public function vipCustomerCount(): int
    {
        return Customer::vip()->count();
    }

    #[Computed]
    public function criticalStockCount(): int
    {
        return SparePart::lowStock(5)->count();
    }

    #[Computed]
    public function revenueChart(): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();

            $wo = WorkOrder::completed()
                ->whereDate('paid_at', $date)
                ->sum('grand_total');

            $ds = DirectSale::whereDate('paid_at', $date)
                ->sum('grand_total');

            return [
                'label' => now()->subDays($daysAgo)->format('d M'),
                'value' => (float) ($wo + $ds),
            ];
        });

        return $days->toArray();
    }

    // ─── Kasir Dashboard ─────────────────────────────────────
    #[Computed]
    public function pendingWoCount(): int
    {
        return WorkOrder::where('status', 'pending')->count();
    }

    #[Computed]
    public function inProgressWoCount(): int
    {
        return WorkOrder::where('status', 'in_progress')->count();
    }

    #[Computed]
    public function recentWorkOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkOrder::with(['customer', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();
    }

    // ─── Staf Gudang Dashboard ───────────────────────────────
    #[Computed]
    public function criticalStockItems(): \Illuminate\Database\Eloquent\Collection
    {
        return SparePart::lowStock(5)->orderBy('quantity_available')->get();
    }

    #[Computed]
    public function totalSparePartCount(): int
    {
        return SparePart::count();
    }

    #[Computed]
    public function todayStockIn(): int
    {
        return StockMovement::where('type', 'IN')
            ->whereDate('created_at', today())
            ->sum('quantity');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-widget')
            ->title('Dashboard');
    }
}
