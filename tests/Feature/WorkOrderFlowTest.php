<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\SparePart;
use App\Models\User;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private WorkOrderService $service;
    private User             $kasir;
    private Customer         $customer;
    private CustomerVehicle  $vehicle;
    private SparePart        $part;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service  = app(WorkOrderService::class);
        $this->kasir    = User::factory()->create(['role' => 'kasir']);
        $this->customer = Customer::factory()->create();
        $this->vehicle  = CustomerVehicle::factory()->create(['customer_id' => $this->customer->id]);
        $this->part     = SparePart::factory()->create(['quantity_available' => 10, 'selling_price' => 100000]);
    }

    /** @test */
    public function test_full_wo_flow_from_create_to_invoice(): void
    {
        // 1. Create WO
        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'Rem blong', 'labour_cost' => 100000],
            [['spare_part_id' => $this->part->id, 'quantity' => 2, 'warranty_days' => 30]],
            $this->kasir
        );

        $this->assertEquals('pending', $wo->status);
        $this->assertEquals(300000, (float) $wo->grand_total); // 2×100k + 100k labour

        // 2. Advance to in_progress
        $this->service->updateStatus($wo, 'in_progress', $this->kasir);
        $wo->refresh();
        $this->assertEquals('in_progress', $wo->status);

        // 3. Complete WO
        $this->service->completeWorkOrder($wo, [
            'payment_method' => 'cash',
            'mechanic_notes' => 'Rem sudah diganti',
        ], $this->kasir);

        $wo->refresh();
        $this->assertEquals('completed', $wo->status);
        $this->assertNotNull($wo->paid_at);

        // 4. Stock harus berkurang 2
        $this->part->refresh();
        $this->assertEquals(8, $this->part->quantity_available);
    }

    /** @test */
    public function test_cancel_wo_and_verify_stock_returned(): void
    {
        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'test', 'labour_cost' => 0],
            [['spare_part_id' => $this->part->id, 'quantity' => 5, 'warranty_days' => 0]],
            $this->kasir
        );

        // Stock dikurangi
        $this->part->refresh();
        $this->assertEquals(5, $this->part->quantity_available);

        // Cancel
        $this->service->cancelWorkOrder($wo, 'Pelanggan membatalkan', $this->kasir);
        $wo->refresh();
        $this->assertEquals('cancelled', $wo->status);

        // Stock dikembalikan
        $this->part->refresh();
        $this->assertEquals(10, $this->part->quantity_available);
    }

    /** @test */
    public function test_audit_log_created_on_wo_status_change(): void
    {
        $initialCount = AuditLog::count();

        $wo = $this->service->createWorkOrder(
            ['customer_id' => $this->customer->id, 'customer_vehicle_id' => $this->vehicle->id, 'complaint' => 'test', 'labour_cost' => 0],
            [],
            $this->kasir
        );

        // Harus ada minimal 1 audit log baru setelah create WO
        $this->assertGreaterThan($initialCount, AuditLog::count());

        // Update status → tambah lagi
        $countAfterCreate = AuditLog::count();
        $this->service->updateStatus($wo, 'in_progress', $this->kasir);
        $this->assertGreaterThan($countAfterCreate, AuditLog::count());
    }
}
