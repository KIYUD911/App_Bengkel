<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;
    private User             $user;
    private SparePart        $part;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InventoryService::class);
        $this->user    = User::factory()->create(['role' => 'staf_gudang']);
        $this->part    = SparePart::factory()->create(['quantity_available' => 10]);
    }

    /** @test */
    public function test_deduct_stock_success(): void
    {
        $this->service->deductStock(
            part: $this->part,
            qty: 3,
            reason: 'work_order',
            user: $this->user,
        );

        $this->part->refresh();
        $this->assertEquals(7, $this->part->quantity_available);
    }

    /** @test */
    public function test_deduct_stock_throws_when_insufficient(): void
    {
        $this->expectException(InsufficientStockException::class);

        $this->service->deductStock(
            part: $this->part,
            qty: 15, // lebih dari stok (10)
            reason: 'work_order',
            user: $this->user,
        );
    }

    /** @test */
    public function test_add_stock_increments_quantity(): void
    {
        $this->service->addStock(
            part: $this->part,
            qty: 5,
            reason: 'restock',
            user: $this->user,
        );

        $this->part->refresh();
        $this->assertEquals(15, $this->part->quantity_available);
    }

    /** @test */
    public function test_rollback_wo_stock_restores_all_items(): void
    {
        // Simulasi WO dengan items — deduct dulu
        $this->service->deductStock($this->part, 4, 'work_order', $this->user);
        $this->part->refresh();
        $this->assertEquals(6, $this->part->quantity_available);

        // Rollback — harus kembali ke 10
        $this->service->rollbackStock($this->part, 4, 'cancel_wo', $this->user);
        $this->part->refresh();
        $this->assertEquals(10, $this->part->quantity_available);
    }
}
