<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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
    public function test_guest_cannot_access_categories(): void
    {
        $response = $this->get(route('categories.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that Kasir role cannot access categories index (gets 403).
     */
    public function test_kasir_cannot_access_categories(): void
    {
        $response = $this->actingAs($this->kasirUser)->get(route('categories.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that Owner role can access categories index.
     */
    public function test_owner_can_access_categories(): void
    {
        $response = $this->actingAs($this->ownerUser)->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertViewIs('categories.index');
    }

    /**
     * Test that Category can be created.
     */
    public function test_can_create_category(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('categories.store'), [
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
            'description' => 'Deskripsi Kategori Test',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
            'description' => 'Deskripsi Kategori Test',
            'is_active' => true,
        ]);
    }

    /**
     * Test category name duplicate validation.
     */
    public function test_cannot_create_duplicate_category_name(): void
    {
        Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('categories.store'), [
            'name' => 'Kategori Test',
            'slug' => 'kategori-test-baru',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Test category can be updated.
     */
    public function test_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Kategori Asli',
            'slug' => 'kategori-asli',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->ownerUser)->put(route('categories.update', $category->id), [
            'name' => 'Kategori Diedit',
            'slug' => 'kategori-diedit',
            'description' => 'Deskripsi Baru',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Kategori Diedit',
            'slug' => 'kategori-diedit',
            'description' => 'Deskripsi Baru',
        ]);
    }

    /**
     * Test soft delete and restore category.
     */
    public function test_can_soft_delete_and_restore_category(): void
    {
        $category = Category::create([
            'name' => 'Kategori Hapus',
            'slug' => 'kategori-hapus',
            'is_active' => true,
        ]);

        // Soft delete
        $response = $this->actingAs($this->ownerUser)->delete(route('categories.destroy', $category->id));
        $response->assertRedirect(route('categories.index'));
        
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);

        // Restore
        $response = $this->actingAs($this->ownerUser)->patch(route('categories.restore', $category->id));
        $response->assertRedirect(route('categories.index', ['trashed' => 1]));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test category search filter.
     */
    public function test_can_search_categories(): void
    {
        Category::create(['name' => 'Kategori Khusus', 'slug' => 'kategori-khusus', 'is_active' => true]);
        Category::create(['name' => 'Kategori Biasa', 'slug' => 'kategori-biasa', 'is_active' => true]);

        $response = $this->actingAs($this->adminUser)->get(route('categories.index', [
            'search' => 'Khusus',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kategori Khusus');
        $response->assertDontSee('Kategori Biasa');
    }
}
