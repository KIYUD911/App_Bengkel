<?php

namespace App\Livewire\DirectSale;

use App\Models\Customer;
use App\Models\SparePart;
use App\Services\DirectSaleService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DirectSaleCreate extends Component
{
    // Toggle tipe pelanggan
    public string $buyerType = 'walk_in'; // 'registered' | 'walk_in'

    // Pelanggan terdaftar
    public string $customerSearch    = '';
    public array  $customerResults   = [];
    public ?int   $selectedCustomerId = null;
    public ?string $selectedCustomerName = null;

    // Walk-in
    public string $walkInName = '';

    // Keranjang belanja
    public string $partSearch      = '';
    public array  $partResults     = [];
    public ?array $selectedPart    = null;
    public int    $cartItemQty     = 1;
    public array  $cartItems       = []; // [{id, name, qty, unit_price, subtotal, unit, available}]

    // Checkout
    public string $paymentMethod = 'tunai';
    public string $notes         = '';

    // Result
    public ?string $saleNumber    = null;
    public ?int    $saleId        = null;
    public bool    $showSuccess   = false;
    public ?string $errorMessage  = null;

    // ─── Customer Search ─────────────────────────────────────

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerResults = [];
            return;
        }
        $this->customerResults = Customer::where('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('phone', 'like', "%{$this->customerSearch}%")
            ->take(5)->get(['id','name','phone','is_vip'])->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->selectedCustomerId   = $id;
        $this->selectedCustomerName = $name;
        $this->customerSearch       = $name;
        $this->customerResults      = [];
    }

    // ─── Part Search ─────────────────────────────────────────

    public function updatedPartSearch(): void
    {
        if (strlen($this->partSearch) < 2) {
            $this->partResults = [];
            return;
        }
        $this->partResults = SparePart::where('name', 'like', "%{$this->partSearch}%")
            ->orWhere('part_code', 'like', "%{$this->partSearch}%")
            ->whereNull('deleted_at')->where('quantity_available', '>', 0)
            ->take(6)->get(['id','name','part_code','selling_price','quantity_available','unit'])->toArray();
    }

    public function selectPart(int $id): void
    {
        $part              = SparePart::findOrFail($id);
        $this->selectedPart = $part->toArray();
        $this->partSearch  = $part->name;
        $this->partResults = [];
        $this->cartItemQty = 1;
    }

    public function addToCart(): void
    {
        if (!$this->selectedPart) return;

        $partId    = $this->selectedPart['id'];
        $available = $this->selectedPart['quantity_available'];

        // Hitung qty yg sudah di keranjang
        $inCart = collect($this->cartItems)->where('id', $partId)->sum('qty');

        if ($inCart + $this->cartItemQty > $available) {
            $this->errorMessage = "Stok {$this->selectedPart['name']} tidak mencukupi. Tersedia: {$available}, Di keranjang: {$inCart}, Diminta: {$this->cartItemQty}";
            return;
        }

        // Cek apakah sudah ada di keranjang
        $existingKey = null;
        foreach ($this->cartItems as $key => $item) {
            if ($item['id'] === $partId) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $this->cartItems[$existingKey]['qty']      += $this->cartItemQty;
            $this->cartItems[$existingKey]['subtotal']  = $this->cartItems[$existingKey]['unit_price'] * $this->cartItems[$existingKey]['qty'];
        } else {
            $this->cartItems[] = [
                'id'         => $partId,
                'name'       => $this->selectedPart['name'],
                'qty'        => $this->cartItemQty,
                'unit_price' => (float) $this->selectedPart['selling_price'],
                'subtotal'   => (float) $this->selectedPart['selling_price'] * $this->cartItemQty,
                'unit'       => $this->selectedPart['unit'],
                'available'  => $available,
            ];
        }

        $this->selectedPart = null;
        $this->partSearch   = '';
        $this->cartItemQty  = 1;
        $this->errorMessage = null;
    }

    public function removeFromCart(int $index): void
    {
        array_splice($this->cartItems, $index, 1);
    }

    public function getGrandTotalProperty(): float
    {
        return collect($this->cartItems)->sum('subtotal');
    }

    // ─── Submit ──────────────────────────────────────────────

    public function processSale(DirectSaleService $service): void
    {
        if (empty($this->cartItems)) {
            $this->errorMessage = 'Keranjang belanja kosong.';
            return;
        }

        try {
            $sale = $service->createDirectSale(
                data: [
                    'customer_id'    => $this->buyerType === 'registered' ? $this->selectedCustomerId : null,
                    'walk_in_name'   => $this->buyerType === 'walk_in' && $this->walkInName ? $this->walkInName : null,
                    'payment_method' => $this->paymentMethod,
                    'notes'          => $this->notes ?: null,
                ],
                items: array_map(fn($item) => [
                    'spare_part_id' => $item['id'],
                    'quantity'      => $item['qty'],
                ], $this->cartItems),
                user: auth()->user(),
            );

            $this->saleNumber  = $sale->sale_number;
            $this->saleId      = $sale->id;
            $this->showSuccess = true;
            $this->cartItems   = [];

        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal memproses penjualan: ' . $e->getMessage();
        }
    }

    public function newTransaction(): void
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.direct-sale.direct-sale-create')
            ->title('Penjualan Langsung');
    }
}
