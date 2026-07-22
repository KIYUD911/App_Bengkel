<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\SparePart;
use App\Models\User;
use App\Services\DirectSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectSaleServiceTest extends TestCase
{
    use RefreshDatabase;

    private DirectSaleService $service;
    private User              $user;
    private SparePart         $part;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DirectSaleService::class);
        $this->user    = User::factory()->create(['role' => 'kasir']);
        $this->part    = SparePart::factory()->create([
            'quantity_available' => 20,
            'selling_price'      => 75000,
        ]);
    }

    /** @test */
    public function test_create_direct_sale_deducts_stock(): void
    {
        $this->service->createDirectSale(
            data: ['walk_in_name' => 'Walk-in Test', 'payment_method' => 'tunai'],
            items: [['spare_part_id' => $this->part->id, 'quantity' => 3]],
            user: $this->user,
        );

        $this->part->refresh();
        $this->assertEquals(17, $this->part->quantity_available);
    }

    /** @test */
    public function test_walkin_sale_does_not_update_customer_stats(): void
    {
        $sale = $this->service->createDirectSale(
            data: ['walk_in_name' => 'Walk-in Tamu', 'payment_method' => 'tunai'],
            items: [['spare_part_id' => $this->part->id, 'quantity' => 1]],
            user: $this->user,
        );

        // Walk-in tidak punya customer_id
        $this->assertNull($sale->customer_id);
    }

    /** @test */
    public function test_registered_customer_sale_updates_total_spent(): void
    {
        $customer = Customer::factory()->create(['total_spent' => 0]);

        $this->service->createDirectSale(
            data: ['customer_id' => $customer->id, 'payment_method' => 'transfer'],
            items: [['spare_part_id' => $this->part->id, 'quantity' => 2]], // 2 × 75000 = 150000
            user: $this->user,
        );

        $customer->refresh();
        $this->assertEquals(150000, (float) $customer->total_spent);
    }
}
