<?php

namespace Tests\Feature;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PaymentMethod;
use App\Models\StockMovement;
use App\Models\CashTransaction;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $ownerUser;
    private User $adminUser;
    private User $kasirUser;
    private Supplier $supplier;
    private Product $product;
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $ownerRole = Role::create(['name' => 'Owner', 'description' => 'Owner role']);
        $adminRole = Role::create(['name' => 'Admin', 'description' => 'Admin role']);
        $kasirRole = Role::create(['name' => 'Kasir', 'description' => 'Kasir role']);

        // Create users
        $this->ownerUser = User::factory()->create(['role_id' => $ownerRole->id]);
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);
        $this->kasirUser = User::factory()->create(['role_id' => $kasirRole->id]);

        // Create initial data
        $this->supplier = Supplier::create([
            'name' => 'Supplier ABC',
            'pic_name' => 'John Doe',
            'phone' => '0812345678',
            'email' => 'supplier@abc.com',
            'address' => 'Supplier Address',
            'description' => 'Main Supplier',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Oli Enduro 4T',
            'barcode' => '123456789',
            'stock' => 10,
            'purchase_price' => 45000,
            'sale_price' => 55000,
            'min_stock' => 5,
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Cash payment',
        ]);
    }

    /**
     * Test route protection for guest.
     */
    public function test_guest_cannot_access_purchases(): void
    {
        $response = $this->get(route('purchases.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test route protection for Kasir.
     */
    public function test_kasir_cannot_access_purchases(): void
    {
        $response = $this->actingAs($this->kasirUser)->get(route('purchases.index'));
        $response->assertStatus(403);
    }

    /**
     * Test index page works for Admin/Owner.
     */
    public function test_admin_can_access_purchases(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('purchases.index'));
        $response->assertStatus(200);
        $response->assertViewIs('purchases.index');
    }

    /**
     * Test creation of purchase (Completed & Paid).
     * Verify:
     * - Stock increases.
     * - Stock Movement (type IN) is saved.
     * - Cash Transaction (type Credit / Kas Keluar) is saved.
     */
    public function test_can_create_completed_paid_purchase(): void
    {
        $invoiceNumber = 'INV-PRC-20260722-0001';

        $response = $this->actingAs($this->adminUser)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'invoice_number' => $invoiceNumber,
            'purchase_date' => '2026-07-22',
            'payment_method_id' => $this->paymentMethod->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'cost_price' => 46000,
                ]
            ]
        ]);

        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('success');

        // Verify purchase header saved
        $purchase = Purchase::where('invoice_number', $invoiceNumber)->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(230000, $purchase->total_amount);

        // Verify purchase item saved
        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'cost_price' => 46000,
            'subtotal' => 230000,
        ]);

        // Verify stock updated (original 10 + 5 = 15)
        $this->product->refresh();
        $this->assertEquals(15, $this->product->stock);

        // Verify capital purchase price updated
        $this->assertEquals(46000, $this->product->purchase_price);

        // Verify stock movement recorded with type 'in'
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 5,
            'referenceable_type' => PurchaseItem::class,
        ]);

        // Verify cash transaction recorded with type 'credit' (Kas Keluar)
        $this->assertDatabaseHas('cash_transactions', [
            'payment_method_id' => $this->paymentMethod->id,
            'type' => 'credit',
            'amount' => 230000,
            'referenceable_type' => Purchase::class,
            'referenceable_id' => $purchase->id,
        ]);
    }

    /**
     * Test edit is restricted if purchase is already completed or paid.
     */
    public function test_cannot_edit_completed_or_paid_purchase(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-PRC-TEST-002',
            'purchase_date' => '2026-07-22',
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->ownerUser)->get(route('purchases.edit', $purchase->id));
        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test purchase updating.
     * Verify stock adjustment and database cleanup.
     */
    public function test_can_update_pending_purchase(): void
    {
        // 1. Create a pending, unpaid purchase first
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-PRC-TEST-003',
            'purchase_date' => '2026-07-22',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_amount' => 90000,
        ]);

        $item = $purchase->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'cost_price' => 45000,
            'subtotal' => 90000,
        ]);

        // Verify stock has not changed yet since status is pending
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);

        // 2. Update status to completed & payment to paid with new item qty
        $response = $this->actingAs($this->ownerUser)->put(route('purchases.update', $purchase->id), [
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-PRC-TEST-003-UPD',
            'purchase_date' => '2026-07-22',
            'payment_method_id' => $this->paymentMethod->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 4, // Qty changed to 4
                    'cost_price' => 45000,
                ]
            ]
        ]);

        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('success');

        // Verify header updated
        $purchase->refresh();
        $this->assertEquals('INV-PRC-TEST-003-UPD', $purchase->invoice_number);
        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals(180000, $purchase->total_amount);

        // Verify stock updated correctly (original 10 + new qty 4 = 14)
        $this->product->refresh();
        $this->assertEquals(14, $this->product->stock);

        // Verify stock movement created for new purchase item
        $newItem = $purchase->items()->first();
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 4,
            'referenceable_type' => PurchaseItem::class,
            'referenceable_id' => $newItem->id,
        ]);

        // Verify cash transaction recorded
        $this->assertDatabaseHas('cash_transactions', [
            'type' => 'credit',
            'amount' => 180000,
            'referenceable_id' => $purchase->id,
        ]);
    }

    /**
     * Test soft delete does not decrement stock of completed purchases.
     */
    public function test_soft_delete_does_not_affect_stock(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-PRC-TEST-004',
            'purchase_date' => '2026-07-22',
            'status' => 'completed',
            'payment_status' => 'paid',
            'total_amount' => 45000,
        ]);

        $item = $purchase->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'cost_price' => 45000,
            'subtotal' => 45000,
        ]);

        // Stock was 10, manually simulate completed purchase impact (+1 = 11)
        $this->product->stock = 11;
        $this->product->save();

        // Perform Soft Delete
        $response = $this->actingAs($this->ownerUser)->delete(route('purchases.destroy', $purchase->id));
        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('purchases', [
            'id' => $purchase->id,
        ]);

        // Verify stock remains untouched (11)
        $this->product->refresh();
        $this->assertEquals(11, $this->product->stock);
    }
}
