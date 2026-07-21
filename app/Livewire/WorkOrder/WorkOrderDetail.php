<?php

namespace App\Livewire\WorkOrder;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidWorkOrderStatusException;
use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\CRMService;
use App\Services\WorkOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.app')]
class WorkOrderDetail extends Component
{
    #[Locked]
    public WorkOrder $workOrder;

    // Tambah Item
    public string $partSearch     = '';
    public array  $partResults    = [];
    public ?int   $selectedPartId = null;
    public ?array $selectedPart   = null;
    public int    $itemQty        = 1;

    // Cancel Modal
    public bool   $showCancelModal = false;
    public string $cancelReason    = '';

    // Feedback
    public bool   $showFeedbackForm = false;
    public int    $feedbackRating   = 5;
    public string $feedbackComment  = '';

    // Payment
    public bool   $showPaymentModal  = false;
    public string $paymentMethod     = 'tunai';

    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    public function mount(WorkOrder $workOrder): void
    {
        $this->workOrder = $workOrder->load(['customer', 'vehicle', 'user', 'items.sparePart', 'feedback']);
    }

    // ─── Add Item ─────────────────────────────────────────────

    public function updatedPartSearch(): void
    {
        if (strlen($this->partSearch) < 2) {
            $this->partResults = [];
            return;
        }
        $this->partResults = SparePart::where('name', 'like', "%{$this->partSearch}%")
            ->orWhere('part_code', 'like', "%{$this->partSearch}%")
            ->whereNull('deleted_at')
            ->take(6)
            ->get(['id', 'name', 'part_code', 'selling_price', 'quantity_available', 'unit'])
            ->toArray();
    }

    public function selectPart(int $id): void
    {
        $part               = SparePart::findOrFail($id);
        $this->selectedPart = $part->toArray();
        $this->selectedPartId = $id;
        $this->partSearch   = $part->name;
        $this->partResults  = [];
        $this->itemQty      = 1;
    }

    public function addItem(WorkOrderService $service): void
    {
        if (! $this->selectedPartId) {
            $this->errorMessage = 'Pilih sparepart terlebih dahulu.';
            return;
        }

        try {
            $part = SparePart::findOrFail($this->selectedPartId);
            $service->addItem($this->workOrder, $part, $this->itemQty, auth()->user());

            $this->workOrder = $this->workOrder->fresh(['customer', 'vehicle', 'user', 'items.sparePart', 'feedback']);
            $this->selectedPart    = null;
            $this->selectedPartId  = null;
            $this->partSearch      = '';
            $this->itemQty         = 1;
            $this->successMessage  = 'Item berhasil ditambahkan.';
            $this->errorMessage    = null;
        } catch (InsufficientStockException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal menambahkan item: ' . $e->getMessage();
        }
    }

    public function removeItem(int $itemId, WorkOrderService $service): void
    {
        $item = WorkOrderItem::findOrFail($itemId);
        try {
            $service->removeItem($item, auth()->user());
            $this->workOrder      = $this->workOrder->fresh(['items.sparePart']);
            $this->successMessage = 'Item berhasil dihapus dan stok dikembalikan.';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    // ─── Update Status ────────────────────────────────────────

    public function updateStatus(string $newStatus, WorkOrderService $service): void
    {
        try {
            $this->workOrder = $service->updateStatus($this->workOrder, $newStatus, auth()->user());
            $this->workOrder->load(['customer', 'vehicle', 'user', 'items.sparePart', 'feedback']);
            $this->successMessage = 'Status berhasil diubah menjadi ' . $newStatus . '.';
        } catch (InvalidWorkOrderStatusException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    // ─── Cancel WO ───────────────────────────────────────────

    public function openCancelModal(): void
    {
        $this->cancelReason    = '';
        $this->showCancelModal = true;
    }

    public function confirmCancel(WorkOrderService $service): void
    {
        if (strlen($this->cancelReason) < 10) {
            $this->errorMessage = 'Alasan pembatalan minimal 10 karakter.';
            return;
        }

        try {
            $this->workOrder = $service->cancelWorkOrder(
                $this->workOrder,
                $this->cancelReason,
                auth()->user(),
            );
            $this->workOrder->load(['customer', 'vehicle', 'user', 'items.sparePart', 'feedback']);
            $this->showCancelModal = false;
            $this->successMessage  = 'Work Order berhasil dibatalkan. Stok telah dikembalikan.';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    // ─── Payment ─────────────────────────────────────────────

    public function processPayment(WorkOrderService $service): void
    {
        try {
            $this->workOrder = $service->processPayment($this->workOrder, $this->paymentMethod, auth()->user());
            $this->workOrder->load(['customer', 'vehicle', 'user', 'items.sparePart', 'feedback']);
            $this->showPaymentModal = false;
            $this->successMessage   = 'Pembayaran berhasil dicatat!';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function downloadInvoice()
    {
        $wo = $this->workOrder->load(['customer', 'vehicle', 'items.sparePart', 'user']);
        $pdf = Pdf::loadView('pdf.work-order-invoice', compact('wo'));
        return response()->streamDownload(
            fn() => print($pdf->output()),
            "invoice-{$wo->wo_number}.pdf"
        );
    }

    // ─── Feedback ─────────────────────────────────────────────

    public function submitFeedback(CRMService $crm): void
    {
        $this->validate(['feedbackRating' => 'required|integer|min:1|max:5']);

        try {
            $crm->submitFeedback(
                $this->workOrder,
                $this->feedbackRating,
                $this->feedbackComment ?: null,
                auth()->user(),
            );
            $this->workOrder->load('feedback');
            $this->showFeedbackForm = false;
            $this->successMessage   = 'Feedback berhasil disimpan. Terima kasih!';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.work-order.work-order-detail')
            ->title('Detail WO ' . $this->workOrder->wo_number);
    }
}
