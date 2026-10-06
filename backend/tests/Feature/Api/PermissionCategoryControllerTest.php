<?php

use App\Models\PermissionCategory;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

describe('index', function () {
    it('returns 403 for a user without a role or permission screen permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/permission-categories')
            ->assertStatus(403);
    });

    it('is available to a user who can create roles', function () {
        Permission::findOrCreate('roles.create', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo('roles.create');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/permission-categories')
            ->assertOk();
    });

    it('lists categories with their permissions and the uncategorized ones', function () {
        $category = PermissionCategory::create(['name' => 'Users']);
        $category->permissions()->attach(Permission::findOrCreate('users.view', 'web')->id);
        Permission::findOrCreate('reports.export', 'web');

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/permission-categories')
            ->assertOk()
            ->assertJsonPath('data.categories.0.name', 'Users')
            ->assertJsonPath('data.categories.0.permissions.0.name', 'users.view')
            ->assertJsonCount(1, 'data.uncategorized')
            ->assertJsonPath('data.uncategorized.0.name', 'reports.export');
    });
});

describe('categories', function () {
    it('creates a category', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/permission-categories', ['name' => 'Inventory'])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Inventory');

        $this->assertDatabaseHas('permission_categories', ['name' => 'Inventory']);
    });

    it('rejects a duplicate category name', function () {
        PermissionCategory::create(['name' => 'Inventory']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/permission-categories', ['name' => 'Inventory'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'], 'data');
    });

    it('renames a category', function () {
        $category = PermissionCategory::create(['name' => 'Old']);

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/permission-categories/{$category->id}", ['name' => 'New'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New');
    });

    it('returns 403 for a user without the permissions.create permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/permission-categories', ['name' => 'Inventory'])
            ->assertStatus(403);
    });
});

describe('permissions', function () {
    it('adds a web-guarded permission under the category', function () {
        $category = PermissionCategory::create(['name' => 'Inventory']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/permission-categories/{$category->id}/permissions", ['name' => ' Stock-Items.View '])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'stock-items.view')
            ->assertJsonPath('data.guard_name', 'web');

        expect($category->permissions()->pluck('name')->all())->toBe(['stock-items.view']);
    });

    it('rejects a name that is not in module.action form', function (string $name) {
        $category = PermissionCategory::create(['name' => 'Inventory']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/permission-categories/{$category->id}/permissions", ['name' => $name])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'], 'data');
    })->with(['stock', 'stock view', 'stock.items.view', '.view']);

    it('rejects a permission that already exists', function () {
        Permission::findOrCreate('users.view', 'web');
        $category = PermissionCategory::create(['name' => 'Users']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/permission-categories/{$category->id}/permissions", ['name' => 'users.view'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'], 'data');
    });

    it('lets a new permission be assigned to a role', function () {
        $category = PermissionCategory::create(['name' => 'Inventory']);
        $admin = adminUser();

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/permission-categories/{$category->id}/permissions", ['name' => 'stock.view'])
            ->assertStatus(201);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/roles', ['name' => 'Storekeeper', 'permissions' => ['stock.view']])
            ->assertStatus(201);
    });
});

describe('seeder', function () {
    it('puts every seeded permission in a category and keeps categories changed later', function () {
        $this->seed(PermissionSeeder::class);

        expect(Permission::count())->toBeGreaterThan(0)
            ->and(DB::table('permission_category_relations')->count())->toBe(Permission::count());

        $moved = PermissionCategory::create(['name' => 'Custom']);
        $usersView = Permission::findByName('users.view', 'web');
        DB::table('permission_category_relations')->where('permission_id', $usersView->id)->update(['per_cate_id' => $moved->id]);

        $this->seed(PermissionSeeder::class);

        expect($moved->permissions()->pluck('name')->all())->toBe(['users.view']);
    });
});
