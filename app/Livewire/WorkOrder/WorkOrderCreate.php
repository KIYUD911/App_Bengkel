<?php

namespace App\Livewire\WorkOrder;

use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Services\CRMService;
use App\Services\WorkOrderService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class WorkOrderCreate extends Component
{
    public int $step = 1;

    // Step 1 — Pelanggan
    public string $customerSearch   = '';
    public ?int   $selectedCustomerId = null;
    public ?Customer $selectedCustomer = null;
    public array  $customerResults  = [];
    public bool   $showNewCustomer  = false;

    // Form pelanggan baru (inline)
    public string $newCustomerName  = '';
    public string $newCustomerPhone = '';

    // Step 2 — Kendaraan
    public ?int  $selectedVehicleId  = null;
    public array $vehicles           = [];
    public bool  $showNewVehicle     = false;

    // Form kendaraan baru (inline)
    public string $newLicensePlate = '';
    public string $newVehicleType  = 'Motor';
    public string $newBrand        = '';
    public string $newModel        = '';
    public string $newYear         = '';

    // Step 3 — Komplain & Biaya
    #[Rule('required|string|min:10')]
    public string $complaint   = '';
    public string $mechanicNotes = '';
    #[Rule('required|numeric|min:0')]
    public string $labourCost  = '0';

    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    // ─── Step 1 — Customer Search ────────────────────────────

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerResults = [];
            return;
        }

        $this->customerResults = Customer::where('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('phone', 'like', "%{$this->customerSearch}%")
            ->take(6)
            ->get(['id', 'name', 'phone', 'is_vip'])
            ->toArray();
    }

    public function selectCustomer(int $id): void
    {
        $this->selectedCustomer   = Customer::with('vehicles')->findOrFail($id);
        $this->selectedCustomerId = $id;
        $this->customerSearch     = $this->selectedCustomer->name;
        $this->customerResults    = [];
        $this->vehicles           = $this->selectedCustomer->vehicles->toArray();
        $this->selectedVehicleId  = count($this->vehicles) === 1 ? $this->vehicles[0]['id'] : null;
    }

    public function saveNewCustomer(CRMService $crm): void
    {
        $this->validate([
            'newCustomerName'  => 'required|string|max:255',
            'newCustomerPhone' => 'required|string|max:20',
        ]);

        $customer = $crm->createCustomer([
            'name'  => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
        ], auth()->user());

        $this->selectCustomer($customer->id);
        $this->showNewCustomer  = false;
        $this->newCustomerName  = '';
        $this->newCustomerPhone = '';
    }

    // ─── Step 2 — Vehicle ────────────────────────────────────

    public function saveNewVehicle(CRMService $crm): void
    {
        $this->validate([
            'newLicensePlate' => 'required|string|max:20',
            'newBrand'        => 'required|string|max:100',
            'newModel'        => 'required|string|max:100',
            'newYear'         => 'required|integer|min:1990|max:' . (date('Y') + 1),
        ]);

        $vehicle = $crm->addVehicle($this->selectedCustomer, [
            'license_plate' => $this->newLicensePlate,
            'vehicle_type'  => $this->newVehicleType,
            'brand'         => $this->newBrand,
            'model'         => $this->newModel,
            'year'          => $this->newYear,
        ], auth()->user());

        $this->vehicles         = Customer::with('vehicles')->find($this->selectedCustomerId)->vehicles->toArray();
        $this->selectedVehicleId = $vehicle->id;
        $this->showNewVehicle   = false;
    }

    // ─── Navigation ──────────────────────────────────────────

    public function nextStep(): void
    {
        if ($this->step === 1 && ! $this->selectedCustomerId) {
            $this->errorMessage = 'Pilih pelanggan terlebih dahulu.';
            return;
        }
        if ($this->step === 2 && ! $this->selectedVehicleId) {
            $this->errorMessage = 'Pilih kendaraan terlebih dahulu.';
            return;
        }
        $this->errorMessage = null;
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->errorMessage = null;
        $this->step = max(1, $this->step - 1);
    }

    // ─── Submit ──────────────────────────────────────────────

    public function submit(WorkOrderService $service): void
    {
        $this->validate([
            'complaint' => 'required|string|min:10',
            'labourCost' => 'required|numeric|min:0',
        ]);

        try {
            $wo = $service->createWorkOrder([
                'customer_id'         => $this->selectedCustomerId,
                'customer_vehicle_id' => $this->selectedVehicleId,
                'complaint'           => $this->complaint,
                'mechanic_notes'      => $this->mechanicNotes ?: null,
                'labour_cost'         => (float) $this->labourCost,
            ], auth()->user());

            $this->redirect(route('work-orders.show', $wo), navigate: true);

        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal membuat Work Order: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.work-order.work-order-create')
            ->title('Buat Work Order Baru');
    }
}
