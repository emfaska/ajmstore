<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $ownerUser;
    private User $adminUser;
    private User $kasirUser;

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
    }

    /**
     * Test that guest is redirected to login page.
     */
    public function test_guest_cannot_access_suppliers(): void
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that Kasir role cannot access suppliers index (gets 403).
     */
    public function test_kasir_cannot_access_suppliers(): void
    {
        $response = $this->actingAs($this->kasirUser)->get(route('suppliers.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that Owner role can access suppliers index.
     */
    public function test_owner_can_access_suppliers(): void
    {
        $response = $this->actingAs($this->ownerUser)->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertViewIs('suppliers.index');
    }

    /**
     * Test that Supplier can be created.
     */
    public function test_can_create_supplier(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('suppliers.store'), [
            'name' => 'Supplier Test',
            'pic_name' => 'Budi PIC',
            'phone' => '08123456789',
            'email' => 'supplier@test.com',
            'address' => 'Alamat Supplier Test',
            'description' => 'Keterangan Supplier Test',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Supplier Test',
            'pic_name' => 'Budi PIC',
            'phone' => '08123456789',
            'email' => 'supplier@test.com',
            'address' => 'Alamat Supplier Test',
            'description' => 'Keterangan Supplier Test',
            'is_active' => true,
        ]);
    }

    /**
     * Test Supplier validation.
     */
    public function test_cannot_create_supplier_without_required_fields(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('suppliers.store'), [
            'name' => '', // Name is required
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Test supplier can be updated.
     */
    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier Asli',
            'pic_name' => 'PIC Asli',
            'phone' => '0812',
            'email' => 'asli@test.com',
            'address' => 'Alamat Asli',
            'description' => 'Deskripsi Asli',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->ownerUser)->put(route('suppliers.update', $supplier->id), [
            'name' => 'Supplier Diedit',
            'pic_name' => 'PIC Diedit',
            'phone' => '0899',
            'email' => 'diedit@test.com',
            'address' => 'Alamat Baru',
            'description' => 'Deskripsi Baru',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Supplier Diedit',
            'pic_name' => 'PIC Diedit',
            'phone' => '0899',
            'email' => 'diedit@test.com',
            'address' => 'Alamat Baru',
            'description' => 'Deskripsi Baru',
        ]);
    }

    /**
     * Test soft delete and restore supplier.
     */
    public function test_can_soft_delete_and_restore_supplier(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier Hapus',
            'is_active' => true,
        ]);

        // Soft delete
        $response = $this->actingAs($this->ownerUser)->delete(route('suppliers.destroy', $supplier->id));
        $response->assertRedirect(route('suppliers.index'));
        
        $this->assertSoftDeleted('suppliers', [
            'id' => $supplier->id,
        ]);

        // Restore
        $response = $this->actingAs($this->ownerUser)->patch(route('suppliers.restore', $supplier->id));
        $response->assertRedirect(route('suppliers.index', ['trashed' => 1]));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test supplier search filter.
     */
    public function test_can_search_suppliers(): void
    {
        Supplier::create(['name' => 'Supplier Khusus', 'pic_name' => 'Andi', 'is_active' => true]);
        Supplier::create(['name' => 'Supplier Biasa', 'pic_name' => 'Budi', 'is_active' => true]);

        // Search by name
        $response = $this->actingAs($this->adminUser)->get(route('suppliers.index', [
            'search' => 'Khusus',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Supplier Khusus');
        $response->assertDontSee('Supplier Biasa');

        // Search by contact/PIC name
        $response = $this->actingAs($this->adminUser)->get(route('suppliers.index', [
            'search' => 'Budi',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Supplier Biasa');
        $response->assertDontSee('Supplier Khusus');
    }
}
