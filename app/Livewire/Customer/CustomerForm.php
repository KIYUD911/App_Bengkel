<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Services\CRMService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class CustomerForm extends Component
{
    public ?int $customerId = null;

    #[Rule('required|string|max:255')]
    public string $name  = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('nullable|email|max:255')]
    public string $email = '';

    #[Rule('nullable|string|max:500')]
    public string $address = '';

    public ?string $errorMessage   = null;
    public ?string $successMessage = null;

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            $this->customerId = $customer->id;
            $this->name    = $customer->name;
            $this->phone   = $customer->phone   ?? '';
            $this->email   = $customer->email   ?? '';
            $this->address = $customer->address ?? '';
        }
    }

    public function save(CRMService $crm): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'phone'   => $this->phone   ?: null,
            'email'   => $this->email   ?: null,
            'address' => $this->address ?: null,
        ];

        try {
            if ($this->customerId) {
                $customer = Customer::findOrFail($this->customerId);
                $crm->updateCustomer($customer, $data, auth()->user());
                $this->successMessage = 'Data pelanggan berhasil diperbarui.';
            } else {
                $customer = $crm->createCustomer($data, auth()->user());
                $this->redirect(route('customers.show', $customer), navigate: true);
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.customer.customer-form')
            ->title($this->customerId ? 'Edit Pelanggan' : 'Tambah Pelanggan');
    }
}
