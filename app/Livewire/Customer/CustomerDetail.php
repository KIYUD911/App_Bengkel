<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Services\CRMService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.app')]
class CustomerDetail extends Component
{
    #[Locked]
    public Customer $customer;

    public string $activeTab = 'vehicles';

    // Form kendaraan baru
    public bool   $showVehicleForm  = false;
    public string $newLicensePlate  = '';
    public string $newVehicleType   = 'Motor';
    public string $newBrand         = '';
    public string $newModel         = '';
    public string $newYear          = '';

    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer->load(['vehicles', 'workOrders.vehicle', 'feedbacks']);
    }

    public function saveVehicle(CRMService $crm): void
    {
        $this->validate([
            'newLicensePlate' => 'required|string|max:20',
            'newBrand'        => 'required|string|max:100',
            'newModel'        => 'required|string|max:100',
            'newYear'         => 'required|integer|min:1990|max:' . (date('Y') + 1),
        ]);

        try {
            $crm->addVehicle($this->customer, [
                'license_plate' => $this->newLicensePlate,
                'vehicle_type'  => $this->newVehicleType,
                'brand'         => $this->newBrand,
                'model'         => $this->newModel,
                'year'          => $this->newYear,
            ], auth()->user());

            $this->customer->load('vehicles');
            $this->showVehicleForm = false;
            $this->newLicensePlate = $this->newBrand = $this->newModel = $this->newYear = '';
            $this->successMessage  = 'Kendaraan berhasil ditambahkan.';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.customer.customer-detail', [
            'activeWarranties' => app(CRMService::class)->getActiveWarranties($this->customer),
        ])->title('Profil: ' . $this->customer->name);
    }
}
