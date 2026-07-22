<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidWorkOrderStatusException;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkOrderService $service;
    private User             $user;
    private Customer         $customer;
    private CustomerVehicle  $vehicle;
    private SparePart        $part;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service  = app(WorkOrderService::class);
        $this->user     = User::factory()->create(['role' => 'kasir']);
        $this->customer = Customer::factory()->create();
        $this->vehicle  = CustomerVehicle::factory()->create(['customer_id' => $this->customer->id]);
        $this->part     = SparePart::factory()->create([
            'quantity_available' => 10,
            'selling_price'      => 50000,
            'purchase_price'     => 35000,
        ]);
    }

    /** @test */
    public function test_generate_wo_number_sequential(): void
    {
        $wo1 = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'Test 1', 'labour_cost' => 0],
            $this->user
        );

        $wo2 = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'Test 2', 'labour_cost' => 0],
            $this->user
        );

        $this->assertNotEquals($wo1->wo_number, $wo2->wo_number);
        $this->assertStringStartsWith('WO-', $wo1->wo_number);
    }

    /** @test */
    public function test_cannot_update_status_backwards(): void
    {
        $this->expectException(InvalidWorkOrderStatusException::class);

        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'test', 'labour_cost' => 0],
            $this->user
        );

        // Advance to in_progress
        $this->service->updateStatus($wo, 'in_progress', $this->user);

        // Try to go backwards to pending — must throw
        $this->service->updateStatus($wo, 'pending', $this->user);
    }

    /** @test */
    public function test_cancel_pending_wo_rolls_back_stock(): void
    {
        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'test', 'labour_cost' => 0],
            $this->user
        );
        $this->service->addItem($wo, $this->part, 3, $this->user);

        // Stock dikurangi saat WO dibuat
        $this->part->refresh();
        $this->assertEquals(7, $this->part->quantity_available);

        // Cancel WO → stok harus kembali
        $this->service->cancelWorkOrder($wo, 'test', $this->user);
        $this->part->refresh();
        $this->assertEquals(10, $this->part->quantity_available);
    }

    /** @test */
    public function test_cancel_completed_wo_throws_exception(): void
    {
        $this->expectException(InvalidWorkOrderStatusException::class);

        $wo = WorkOrder::factory()->create([
            'status'      => 'completed',
            'customer_id' => $this->customer->id,
        ]);

        $this->service->cancelWorkOrder($wo, 'test', $this->user);
    }

    /** @test */
    public function test_grand_total_recalculated_after_add_item(): void
    {
        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'test', 'labour_cost' => 50000],
            $this->user
        );

        $this->service->addItem($wo, $this->part, 2, $this->user);

        $wo->refresh();
        // grand_total = (2 × 50000) + 50000 (labour) = 150000
        $this->assertEquals(150000, (float) $wo->grand_total);
    }
}
