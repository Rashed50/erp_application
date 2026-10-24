<?php

use App\Models\Customer;
use App\Models\Employee;
use App\Models\EmployeeFile;
use App\Models\Sale;
use App\Services\EmployeeFileService;
use App\Services\FileStorageService;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores public uploads on the configured public disk', function () {
    config(['filesystems.uploads.public_disk' => 'uploads-test']);
    Storage::fake('uploads-test');
    Storage::fake('public');
    (new ChartOfAccountSeeder)->run();
    $customer = Customer::factory()->create();

    $this->actingAs(adminUser(), 'sanctum')
        ->postJson('/api/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'SINV-'.fake()->unique()->numerify('#####'),
            'issue_date' => now()->toDateString(),
            'attachment' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
            'items' => [['item_name' => 'Consulting', 'qty' => 1, 'unit_price' => 100]],
        ])
        ->assertStatus(201);

    $attachment = Sale::sole()->attachment;
    Storage::disk('uploads-test')->assertExists($attachment);
    Storage::disk('public')->assertMissing($attachment);
});

it('stores private uploads on the configured private disk', function () {
    config(['filesystems.uploads.private_disk' => 'private-test']);
    Storage::fake('private-test');
    Storage::fake('local');
    $this->actingAs(adminUser(), 'sanctum');

    $file = app(EmployeeFileService::class)->store(
        Employee::factory()->create(),
        UploadedFile::fake()->create('nid.pdf', 100, 'application/pdf'),
        'NID',
    );

    Storage::disk('private-test')->assertExists($file->file_path);
    Storage::disk('local')->assertMissing($file->file_path);
    expect(EmployeeFile::sole()->file_path)->toBe($file->file_path);
});

it('keeps the current file when nothing new is uploaded and no removal is asked', function () {
    Storage::fake('public');
    Storage::disk('public')->put('sales/invoice.pdf', 'pdf');

    $path = app(FileStorageService::class)->replace('sales/invoice.pdf', null, false, 'sales');

    expect($path)->toBe('sales/invoice.pdf');
    Storage::disk('public')->assertExists('sales/invoice.pdf');
});
